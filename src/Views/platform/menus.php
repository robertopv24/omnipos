<div class="content-header d-flex justify-between align-center mb-2">
    <div>
        <h1 class="text-gradient mb-05 border-none"><i class="fa fa-sitemap"></i> <?= __('Editor de Menús') ?></h1>
        <p class="text-dim"><?= __('Configura la estructura jerárquica de la navegación y define permisos de visibilidad.') ?></p>
    </div>
    <div class="d-flex gap-1">
        <button class="btn btn-secondary btn-sm" onclick="exportConfig()"><i class="fa fa-download"></i> <?= __('Exportar Config') ?></button>
        <button class="btn btn-primary btn-sm" onclick="openMenuModal()"><i class="fa fa-plus"></i> <?= __('Nuevo Item') ?></button>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="glass-widget p-2">
            <h3 class="mb-1 text-pure font-700"><?= __('Navegación del Sistema') ?></h3>
            <div class="table-responsive">
                <table class="table w-full">
                    <thead>
                        <tr class="text-left border-bottom border-glass">
                            <th class="p-1 text-dim uppercase text-xs"><?= __('Tipo') ?></th>
                            <th class="p-1 text-dim uppercase text-xs"><?= __('Elemento / Título') ?></th>
                            <th class="p-1 text-dim uppercase text-xs"><?= __('URL / Ruta') ?></th>
                            <th class="p-1 text-dim uppercase text-xs"><?= __('Visibilidad') ?></th>
                            <th class="p-1 text-dim uppercase text-xs"><?= __('Permisos') ?></th>
                            <th class="p-1 text-right text-dim uppercase text-xs"><?= __('Acciones') ?></th>
                        </tr>
                    </thead>
                    <tbody id="menu-table-body">
                        <?php foreach ($menus as $menu): ?>
                        <tr class="border-bottom border-glass hover-bright <?= $menu['parent_id'] ? 'bg-black-10' : 'font-700' ?>" data-id="<?= $menu['id'] ?>">
                            <td class="p-1">
                                <span class="badge badge-secondary text-xs"><?= strtoupper($menu['type']) ?></span>
                            </td>
                            <td class="p-1">
                                <?php if($menu['parent_id']): ?>
                                    <span class="ml-2 text-dim">└</span>
                                <?php endif; ?>
                                <i class="<?= htmlspecialchars($menu['icon'] ?? 'fa fa-circle') ?> text-xs mr-05 <?= $menu['parent_id'] ? 'text-dim' : 'text-primary' ?>"></i>
                                <span class="<?= $menu['parent_id'] ? 'text-dim font-400' : 'text-bright' ?>"><?= htmlspecialchars($menu['title']) ?></span>
                            </td>
                            <td class="p-1"><code class="text-xs text-info"><?= htmlspecialchars($menu['url']) ?></code></td>
                            <td class="p-1">
                                <span class="badge <?= $menu['visibility'] === 'public' ? 'badge-success' : 'badge-warning' ?> text-xs uppercase"><?= __($menu['visibility']) ?></span>
                            </td>
                            <td class="p-1">
                                <span class="text-xs text-dim"><?= $menu['permission_name'] ? htmlspecialchars($menu['permission_name']) : '-' ?></span>
                            </td>
                            <td class="p-1 text-right">
                                <div class="d-flex gap-05 justify-end">
                                    <button class="btn btn-sm btn-secondary px-05" onclick='openMenuModal(<?= json_encode($menu) ?>)'><i class="fa fa-edit"></i></button>
                                    <button class="btn btn-sm btn-outline-danger px-05" onclick="deleteMenu('<?= $menu['id'] ?>')"><i class="fa fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Menu Editor Modal -->
<div class="modal fade" id="menuModal" tabindex="-1" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.8); z-index:9999; align-items:center; justify-content:center;">
    <div class="modal-content" style="background:var(--sidebar-bg); padding:30px; border-radius:8px; width:550px; border:1px solid var(--border);">
        <h2 id="modalTitle" class="text-gradient mb-2">Nuevo Item de Menú</h2>
        <form action="<?= url('/platform/menus/save') ?>" method="POST">
            <input type="hidden" name="id" id="item-id">
            
            <div class="row">
                <div class="col-md-6 mb-1">
                    <label class="prop-label">Título (I18n Key)</label>
                    <input type="text" name="title" id="item-title" class="prop-input" required placeholder="dashboard, users, etc.">
                </div>
                <div class="col-md-6 mb-1">
                    <label class="prop-label">Icono (FontAwesome)</label>
                    <input type="text" name="icon" id="item-icon" class="prop-input" placeholder="fa fa-home">
                </div>
            </div>

            <div class="mb-1">
                <label class="prop-label">URL / Ruta</label>
                <input type="text" name="url" id="item-url" class="prop-input" required placeholder="/ruta/de/ejemplo">
            </div>

            <div class="row">
                <div class="col-md-6 mb-1">
                    <label class="prop-label">Tipo de Menú</label>
                    <select name="type" id="item-type" class="prop-input">
                        <option value="sidebar">Barra Lateral (Sidebar)</option>
                        <option value="header">Pie de Cabecera (Header)</option>
                        <option value="footer">Pie de Página (Footer)</option>
                    </select>
                </div>
                <div class="col-md-6 mb-1">
                    <label class="prop-label">Padre (Relación)</label>
                    <select name="parent_id" id="item-parent" class="prop-input">
                        <option value="">(Ninguno / Raíz)</option>
                        <?php foreach ($parents as $p): ?>
                            <option value="<?= $p['id'] ?>">[<?= strtoupper($p['type']) ?>] <?= $p['title'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-1">
                    <label class="prop-label">Visibilidad</label>
                    <select name="visibility" id="item-visibility" class="prop-input">
                        <option value="public">Público</option>
                        <option value="authenticated">Autenticado</option>
                        <option value="private">Privado (Permiso)</option>
                        <option value="admin">Administrador General</option>
                    </select>
                </div>
                <div class="col-md-6 mb-1">
                    <label class="prop-label">Permiso Requerido</label>
                    <select name="required_permission_id" id="item-permission" class="prop-input">
                        <option value="">(Sin Permiso)</option>
                        <?php foreach ($permissions as $perm): ?>
                            <option value="<?= $perm['id'] ?>"><?= $perm['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="row mt-1">
                <div class="col-md-6">
                    <label class="prop-label">Posición</label>
                    <input type="number" name="position" id="item-position" class="prop-input" value="0">
                </div>
                <div class="col-md-6 d-flex align-center" style="padding-top: 1.5rem;">
                    <label class="d-flex align-center gap-05" style="cursor:pointer;">
                        <input type="checkbox" name="is_active" id="item-active" checked>
                        <span class="text-sm text-dim">Activo</span>
                    </label>
                </div>
            </div>

            <div class="d-flex justify-end gap-1 mt-3">
                <button type="button" class="btn btn-secondary" onclick="closeMenuModal()">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar Menú</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openMenuModal(data = null) {
        const modal = document.getElementById('menuModal');
        const title = document.getElementById('modalTitle');
        const idField = document.getElementById('item-id');
        
        modal.style.display = 'flex';
        
        if (data) {
            title.innerText = 'Editar Item de Menú';
            idField.value = data.id;
            document.getElementById('item-title').value = data.title;
            document.getElementById('item-url').value = data.url;
            document.getElementById('item-icon').value = data.icon;
            document.getElementById('item-parent').value = data.parent_id || '';
            document.getElementById('item-position').value = data.position;
            document.getElementById('item-type').value = data.type;
            document.getElementById('item-visibility').value = data.visibility;
            document.getElementById('item-permission').value = data.required_permission_id || '';
            document.getElementById('item-active').checked = parseInt(data.is_active) === 1;
        } else {
            title.innerText = 'Nuevo Item de Menú';
            idField.value = '';
            document.getElementById('item-title').value = '';
            document.getElementById('item-url').value = '';
            document.getElementById('item-icon').value = 'fa fa-circle';
            document.getElementById('item-parent').value = '';
            document.getElementById('item-position').value = 0;
            document.getElementById('item-type').value = 'sidebar';
            document.getElementById('item-visibility').value = 'public';
            document.getElementById('item-permission').value = '';
            document.getElementById('item-active').checked = true;
        }
    }

    function closeMenuModal() {
        document.getElementById('menuModal').style.display = 'none';
    }

    async function deleteMenu(id) {
        if (!confirm('¿Estás seguro de eliminar este ítem? Esto podría afectar la navegación de los usuarios.')) return;
        
        try {
            const resp = await fetch('<?= url("/platform/menus/delete") ?>?id=' + id, {
                method: 'POST'
            });
            const data = await resp.json();
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.error);
            }
        } catch (e) {
            alert('Error al conectar con el servidor');
        }
    }
    
    function exportConfig() {
        alert('Funcionalidad de exportación en desarrollo.');
    }
</script>

<style>
.ml-2 { margin-left: 1.5rem; }
.bg-black-10 { background: rgba(0,0,0,0.1); }
.mr-05 { margin-right: 0.5rem; }

/* Modal helper styles (shared with builder) */
.prop-label {
    display: block;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    color: var(--text-dim);
    margin-bottom: 0.5rem;
}

.prop-input {
    width: 100%;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid var(--border);
    border-radius: 4px;
    padding: 0.5rem;
    color: var(--text-bright);
    font-size: 0.9rem;
}

.prop-input:focus {
    outline: none;
    border-color: var(--primary);
    background: rgba(255, 255, 255, 0.1);
}
</style>
