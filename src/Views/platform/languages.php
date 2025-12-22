<div class="content-header d-flex justify-between align-center mb-2">
    <div>
        <h1 class="text-gradient mb-05 border-none"><i class="fa fa-language"></i> <?= __('Gestión de Idiomas') ?></h1>
        <p class="text-dim"><?= __('Personaliza las traducciones locales y activa nuevos idiomas para la plataforma.') ?></p>
    </div>
    <div class="d-flex gap-1">
        <button class="btn btn-secondary btn-sm" onclick="location.reload()"><i class="fa fa-sync"></i> <?= __('Refrescar') ?></button>
        <button class="btn btn-primary btn-sm" onclick="openAddLangModal()"><i class="fa fa-plus"></i> <?= __('Agregar Idioma') ?></button>
    </div>
</div>

<div class="row">
    <div class="col-md-5">
        <div class="glass-widget p-2 h-full">
            <h3 class="mb-1 text-pure font-700"><?= __('Idiomas Instalados') ?></h3>
            <div class="table-responsive">
                <table class="table w-full">
                    <thead>
                        <tr class="text-left border-bottom border-glass">
                            <th class="p-1 text-dim uppercase text-xs">ISO</th>
                            <th class="p-1 text-dim uppercase text-xs">Idioma</th>
                            <th class="p-1 text-dim uppercase text-xs">Progreso</th>
                            <th class="p-1 text-right text-dim uppercase text-xs">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="lang-table-body">
                        <?php foreach ($languages as $l): ?>
                        <tr class="border-bottom border-glass hover-bright cursor-pointer" onclick="loadTranslations('<?= $l['iso'] ?>')">
                            <td class="p-1"><span class="badge badge-info uppercase"><?= $l['iso'] ?></span></td>
                            <td class="p-1">
                                <div class="font-600"><?= htmlspecialchars($l['name']) ?></div>
                                <div class="text-xs text-dim"><?= $l['count'] ?> llaves</div>
                            </td>
                            <td class="p-1">
                                <div class="progress-bar bg-glass" style="height: 6px; border-radius: 3px; overflow: hidden; width: 60px;">
                                    <div class="bg-primary" style="width: <?= $l['progress'] ?>%; height: 100%;"></div>
                                </div>
                                <span class="text-xs text-muted"><?= $l['progress'] ?>%</span>
                            </td>
                            <td class="p-1 text-right">
                                <button class="btn btn-sm btn-outline-danger px-05" onclick="event.stopPropagation(); deleteLang('<?= $l['iso'] ?>')"><i class="fa fa-trash"></i></button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="glass-widget p-2 h-full" id="translation-editor-container" style="display: none;">
            <div class="d-flex justify-between align-center mb-1">
                <h3 class="mb-0 text-pure font-700"><?= __('Editor de Traducciones') ?> (<span id="current-iso-edit">--</span>)</h3>
                <div class="input-group" style="width: 200px;">
                    <input type="text" id="search-keys" class="prop-input py-05" placeholder="Buscar llave..." onkeyup="filterKeys()">
                </div>
            </div>

            <div id="keys-list" class="custom-scrollbar" style="max-height: 600px; overflow-y: auto; padding-right: 0.5rem;">
                <!-- Keys will be loaded here -->
            </div>
            
            <div class="mt-2 pt-1 border-top border-glass d-flex justify-between">
                <button class="btn btn-secondary btn-sm" onclick="addNewKey()"><i class="fa fa-plus"></i> Nueva Llave</button>
                <p class="text-xs text-dim italic m-0 flex-center">
                    <i class="fa fa-info-circle text-info mr-05"></i>Los cambios se guardan automáticamente al perder el foco.
                </p>
            </div>
        </div>
        
        <div class="glass-widget p-4 h-full flex-center flex-column text-center" id="no-lang-selected">
            <i class="fa fa-language text-dim mb-1" style="font-size: 3rem; opacity: 0.2;"></i>
            <p class="text-dim"><?= __('Selecciona un idioma de la lista para editar sus traducciones.') ?></p>
        </div>
    </div>
</div>

<!-- Add Language Modal -->
<div class="modal fade" id="addLangModal" tabindex="-1" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.8); z-index:9999; align-items:center; justify-content:center;">
    <div class="modal-content" style="background:var(--sidebar-bg); padding:30px; border-radius:8px; width:400px; border:1px solid var(--border);">
        <h2 class="text-gradient mb-2">Agregar Nuevo Idioma</h2>
        <form action="<?= url('/platform/languages/save') ?>" method="POST">
            <div class="mb-2">
                <label class="prop-label">Código ISO (2 letras)</label>
                <input type="text" name="iso" class="prop-input" required maxlength="2" placeholder="ej: pt, fr, it">
                <small class="text-dim mt-05 d-block">Se creará un nuevo archivo .json en src/I18n/</small>
            </div>
            <div class="d-flex justify-end gap-1 mt-3">
                <button type="button" class="btn btn-secondary" onclick="closeAddLangModal()">Cancelar</button>
                <button type="submit" class="btn btn-primary">Crear Idioma</button>
            </div>
        </form>
    </div>
</div>

<script>
    let currentTranslations = {};
    let currentIso = '';

    function openAddLangModal() {
        document.getElementById('addLangModal').style.display = 'flex';
    }
    function closeAddLangModal() {
        document.getElementById('addLangModal').style.display = 'none';
    }

    async function loadTranslations(iso) {
        currentIso = iso;
        document.getElementById('no-lang-selected').style.display = 'none';
        document.getElementById('translation-editor-container').style.display = 'block';
        document.getElementById('current-iso-edit').innerText = iso.toUpperCase();
        document.getElementById('keys-list').innerHTML = '<div class="text-center py-4"><i class="fa fa-spinner fa-spin"></i> Cargando...</div>';

        try {
            const resp = await fetch('<?= url("/platform/languages/get") ?>?iso=' + iso);
            const data = await resp.json();
            if (data.success) {
                currentTranslations = data.translations;
                renderKeys();
            }
        } catch (e) { console.error(e); }
    }

    function renderKeys() {
        const container = document.getElementById('keys-list');
        container.innerHTML = '';
        
        Object.keys(currentTranslations).sort().forEach(key => {
            const val = currentTranslations[key];
            const div = document.createElement('div');
            div.className = 'translation-item mb-1 p-1 bg-glass-hover rounded';
            div.dataset.key = key;
            div.innerHTML = `
                <div class="d-flex justify-between align-center mb-05">
                    <span class="text-xs font-700 text-primary uppercase">${key}</span>
                    <i class="fa fa-check text-success text-xs status-icon" style="display:none;"></i>
                </div>
                <textarea class="prop-input py-05 text-sm" rows="1" onblur="updateKey('${key}', this.value)" oninput="this.style.height = '';this.style.height = this.scrollHeight + 'px'">${val}</textarea>
            `;
            container.appendChild(div);
        });
    }

    function filterKeys() {
        const q = document.getElementById('search-keys').value.toLowerCase();
        document.querySelectorAll('.translation-item').forEach(item => {
            const key = item.dataset.key.toLowerCase();
            const val = item.querySelector('textarea').value.toLowerCase();
            item.style.display = (key.includes(q) || val.includes(q)) ? 'block' : 'none';
        });
    }

    async function updateKey(key, value) {
        if (currentTranslations[key] === value) return;
        
        const item = document.querySelector(`.translation-item[data-key="${key}"]`);
        const statusIcon = item.querySelector('.status-icon');
        
        try {
            const resp = await fetch('<?= url("/platform/languages/update-key") ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ iso: currentIso, key: key, value: value })
            });
            const data = await resp.json();
            if (data.success) {
                currentTranslations[key] = value;
                statusIcon.style.display = 'inline-block';
                setTimeout(() => statusIcon.style.display = 'none', 2000);
            }
        } catch (e) { alert('Error al guardar llave: ' + key); }
    }

    async function deleteLang(iso) {
        if (!confirm(`¿Estás seguro de eliminar el idioma "${iso.toUpperCase()}"? Se borrará el archivo de traducciones permanentemente.`)) return;
        
        try {
            const resp = await fetch('<?= url("/platform/languages/delete") ?>?iso=' + iso, { method: 'POST' });
            const data = await resp.json();
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.error);
            }
        } catch (e) { alert('Error al eliminar idioma'); }
    }

    function addNewKey() {
        const key = prompt('Introduce el nombre de la nueva llave (ej: menu_reports):');
        if (!key) return;
        
        if (currentTranslations[key] !== undefined) {
            alert('La llave ya existe.');
            return;
        }

        currentTranslations[key] = '';
        renderKeys();
        // Focus the new key
        const item = document.querySelector(`.translation-item[data-key="${key}"] textarea`);
        if (item) item.focus();
    }
</script>

<style>
.bg-glass-hover:hover { background: rgba(255, 255, 255, 0.05); }
.status-icon { transition: opacity 0.3s; }

/* Styling consistency */
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
    resize: none;
}

.prop-input:focus {
    outline: none;
    border-color: var(--primary);
    background: rgba(255, 255, 255, 0.1);
}

.badge-info { background: var(--info); color: white; }
</style>
