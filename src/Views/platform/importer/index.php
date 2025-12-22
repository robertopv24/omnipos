<?php
/**
 * Importador de Vistas Huérfanas
 */
?>
<div class="container-fluid p-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1><i class="fa fa-search"></i> Importador de Vistas Huérfanas</h1>
            <p class="text-muted">Escanea el sistema en busca de archivos PHP que no están vinculados a ninguna ruta ni menú.</p>
        </div>
    </div>

    <div class="card glass-effect mb-4">
        <div class="card-body">
            <button id="btnScan" class="btn btn-primary btn-lg">
                <i class="fa fa-radar"></i> Escanear Sistema
            </button>
            <div id="scanStatus" class="mt-2 text-info" style="display:none;">Escaneando...</div>
        </div>
    </div>

    <div id="resultsArea" style="display:none;">
        <h3>Vistas Encontradas</h3>
        <div class="table-responsive">
            <table class="table table-dark table-hover">
                <thead>
                    <tr>
                        <th>Archivo</th>
                        <th>Ruta Sugerida</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody id="orphansTable">
                    <!-- Results here -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Bind -->
<div class="modal fade" id="bindModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content glass-effect">
            <div class="modal-header">
                <h5 class="modal-title">Vincular Vista</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="bindForm">
                    <input type="hidden" id="bindFile">
                    <div class="mb-3">
                        <label>URL (Slug)</label>
                        <input type="text" class="form-control" id="bindSlug" required placeholder="/mi-ruta">
                    </div>
                    <div class="mb-3">
                        <label>Título del Menú</label>
                        <input type="text" class="form-control" id="bindTitle" required>
                    </div>
                    <div class="mb-3">
                        <label>Grupo de Menú</label>
                        <select class="form-select" id="bindGroup">
                            <option value="">-- No agregar al menú --</option>
                            <option value="Configuración">Configuración</option>
                            <option value="Ventas">Ventas</option>
                            <option value="Inventario">Inventario</option>
                            <option value="Gestión SaaS">Gestión SaaS</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" id="btnSaveBind">Guardar y Vincular</button>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('btnScan').addEventListener('click', async () => {
    const status = document.getElementById('scanStatus');
    status.style.display = 'block';
    
    try {
        const res = await fetch('<?= url("/platform/importer/scan") ?>', { method: 'POST' });
        const data = await res.json();
        
        const tbody = document.getElementById('orphansTable');
        tbody.innerHTML = '';
        
        data.orphans.forEach(item => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${item.path}</td>
                <td>/${item.path.replace('src/Views/', '').replace('.php', '')}</td>
                <td>
                    <button class="btn btn-sm btn-outline-success" onclick="openBind('${item.path}')">
                        <i class="fa fa-link"></i> Vincular
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
        });
        
        document.getElementById('resultsArea').style.display = 'block';
        status.style.display = 'none';
    } catch (e) {
        alert('Error al escanear: ' + e.message);
    }
});

function openBind(path) {
    document.getElementById('bindFile').value = path;
    // Suggest slug
    const slug = '/' + path.split('/').pop().replace('.php', '');
    document.getElementById('bindSlug').value = slug;
    
    // Suggest title
    const title = slug.replace('/', '').charAt(0).toUpperCase() + slug.slice(2);
    document.getElementById('bindTitle').value = title;
    
    new bootstrap.Modal(document.getElementById('bindModal')).show();
}

document.getElementById('btnSaveBind').addEventListener('click', async () => {
    const payload = {
        viewPath: document.getElementById('bindFile').value,
        slug: document.getElementById('bindSlug').value,
        title: document.getElementById('bindTitle').value,
        menuGroup: document.getElementById('bindGroup').value
    };

    try {
        const res = await fetch('<?= url("/platform/importer/bind") ?>', { 
            method: 'POST',
            body: JSON.stringify(payload)
        });
        const data = await res.json();
        
        if (data.success) {
            alert('¡Vista vinculada correctamente!');
            window.location.reload();
        } else {
            alert('Error: ' + data.error);
        }
    } catch (e) {
        alert('Error de red');
    }
});
</script>
