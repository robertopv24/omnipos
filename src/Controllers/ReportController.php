<?php

namespace OmniPOS\Controllers;

use OmniPOS\Core\Request;
use OmniPOS\Core\Response;
use OmniPOS\Core\Session;
use OmniPOS\Core\Database;
use OmniPOS\Services\MenuService;
use PDO;

class ReportController extends Controller
{
    public function index(Request $request, Response $response)
    {
        $this->checkPermission('view_reports');
        return $this->render('reports/index', [
            'title' => 'Reportes y Auditoría'
        ]);
    }

    /**
     * Exporta el Libro de Ventas / Impuestos.
     */
    public function exportTaxLedger(Request $request, Response $response)
    {
        $this->checkPermission('view_reports');
        $businessId = Session::get('business_id');
        $format = $request->get('format', 'excel'); // excel/pdf
        $startDate = $request->get('start_date', date('Y-m-01'));
        $endDate = $request->get('end_date', date('Y-m-d'));

        $pdo = Database::connect();
        $sql = "SELECT tl.*, o.id as order_ref 
                FROM tax_ledger tl
                LEFT JOIN orders o ON tl.reference_id = o.id
                WHERE tl.business_id = :bid 
                AND tl.created_at BETWEEN :start AND :end
                ORDER BY tl.created_at ASC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['bid' => $businessId, 'start' => "{$startDate} 00:00:00", 'end' => "{$endDate} 23:59:59"]);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($format === 'pdf') {
            return $this->generatePdf(' Libro de Ventas / IVA', $data);
        }

        return $this->generateExcel('Libro_Ventas', $data);
    }

    /**
     * Reporte de Rentabilidad Detallado.
     */
    public function profitability(Request $request, Response $response)
    {
        $this->checkPermission('view_reports');
        $this->view->setLayout('admin');

        $businessId = Session::get('business_id');
        $startDate = $request->get('start_date', date('Y-m-01'));
        $endDate = $request->get('end_date', date('Y-m-d'));
        $startSql = $startDate . " 00:00:00";
        $endSql = $endDate . " 23:59:59";

        $pdo = Database::connect();

        // 1. Ganancia Bruta (Ventas - Devoluciones)
        $sqlSales = "SELECT SUM(total_price) FROM orders WHERE business_id = :bid AND status IN ('paid', 'delivered') AND created_at BETWEEN :start AND :end";
        $stmt = $pdo->prepare($sqlSales);
        $stmt->execute(['bid' => $businessId, 'start' => $startSql, 'end' => $endSql]);
        $totalSales = $stmt->fetchColumn() ?: 0;

        // 2. Costo de Ventas (COGS) - Usando Trazabilidad (FIFO)
        $sqlCogs = "SELECT SUM(im.quantity * ib.unit_cost) 
                    FROM inventory_movements im 
                    JOIN inventory_batches ib ON im.batch_id = ib.id
                    JOIN orders o ON im.reference_id = o.id
                    WHERE o.business_id = :bid 
                    AND o.status IN ('paid', 'delivered') 
                    AND o.created_at BETWEEN :start AND :end
                    AND im.type = 'exit' 
                    AND im.reference_type LIKE 'sale%'";
        $stmt = $pdo->prepare($sqlCogs);
        $stmt->execute(['bid' => $businessId, 'start' => $startSql, 'end' => $endSql]);
        $totalCogs = $stmt->fetchColumn() ?: 0;

        // 3. Gastos Operativos (Egresos manuales, Caja Chica)
        $sqlExpenses = "SELECT SUM(amount) FROM transactions 
                        WHERE business_id = :bid AND type = 'expense' 
                        AND reference_type NOT IN ('order', 'adjustment')
                        AND created_at BETWEEN :start AND :end";
        $stmt = $pdo->prepare($sqlExpenses);
        $stmt->execute(['bid' => $businessId, 'start' => $startSql, 'end' => $endSql]);
        $totalExpenses = $stmt->fetchColumn() ?: 0;

        // 4. Nómina
        $sqlPayroll = "SELECT SUM(amount) FROM payroll_payments WHERE business_id = :bid AND created_at BETWEEN :start AND :end";
        $stmt = $pdo->prepare($sqlPayroll);
        $stmt->execute(['bid' => $businessId, 'start' => $startSql, 'end' => $endSql]);
        $totalPayroll = $stmt->fetchColumn() ?: 0;

        $metrics = [
            'total_sales' => $totalSales,
            'total_cogs' => $totalCogs,
            'total_expenses' => $totalExpenses,
            'total_payroll' => $totalPayroll,
            'gross_profit' => $totalSales - $totalCogs,
            'net_profit' => $totalSales - $totalCogs - $totalExpenses - $totalPayroll
        ];

        return $this->render('reports/profitability', [
            'title' => 'Análisis de Rentabilidad',
            'metrics' => $metrics,
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);
    }

    private function generateExcel(string $filename, array $data)
    {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '_' . date('Ymd') . '.csv"');
        
        $output = fopen('php://output', 'w');
        if (!empty($data)) {
            fputcsv($output, array_keys($data[0])); // Headers
            foreach ($data as $row) {
                fputcsv($output, $row);
            }
        }
        fclose($output);
        exit();
    }

    private function generatePdf(string $title, array $data)
    {
        // Implementación básica con mPDF o similar en el futuro. 
        // Por ahora simulamos el stream para paridad de arquitectura.
        echo "<h1>{$title}</h1>";
        echo "<table border='1'><thead><tr>";
        if (!empty($data)) {
            foreach (array_keys($data[0]) as $h) echo "<th>$h</th>";
            echo "</tr></thead><tbody>";
            foreach ($data as $row) {
                echo "<tr>";
                foreach ($row as $v) echo "<td>$v</td>";
                echo "</tr>";
            }
        }
        echo "</tbody></table>";
        exit();
    }
}
