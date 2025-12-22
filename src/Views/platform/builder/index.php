<?php
/**
 * World-Class Visual Page Builder - Pro Edition
 */
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<style>
    :root {
        --sidebar-bg: #1e293b;
        --toolbar-bg: #0f172a;
        --canvas-bg: #f1f5f9;
        --accent: #3b82f6;
        --border: rgba(255,255,255,0.1);
    }
    body { background: var(--toolbar-bg); color: #f8fafc; overflow: hidden; font-family: 'Inter', sans-serif; }
    
    /* Layout */
    .app-shell { display: flex; flex-direction: column; height: 100vh; }
    .top-bar { height: 60px; background: var(--toolbar-bg); border-bottom: 1px solid var(--border); display: flex; align-items: center; padding: 0 20px; justify-content: space-between; }
    .main-grid { flex: 1; display: grid; grid-template-columns: 280px 1fr 320px; overflow: hidden; }
    
    /* Sidebar Palette */
    .sidebar-left { background: var(--sidebar-bg); border-right: 1px solid var(--border); overflow-y: auto; display: flex; flex-direction: column; }
    .sidebar-right { background: var(--sidebar-bg); border-left: 1px solid var(--border); overflow-y: auto; padding: 20px; }
    
    /* Canvas / Workspace */
    /* Canvas / Workspace */
    .workspace { background: var(--canvas-bg); overflow-y: auto; display: flex; flex-direction: column; align-items: center; padding: 40px; }
    #canvasWrapper { 
        width: 100%; max-width: 1140px; min-height: 800px; 
        background: white; border-radius: 4px; box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
    }
    
    /* Live Canvas Styles */
    #mainCanvas { color: #1e293b; }
    .node-item { position: relative; transition: all 0.2s; }
    .node-item:hover { outline: 1px dashed var(--accent); outline-offset: -1px; z-index: 50; }
    .node-item.selected { outline: 2px solid var(--accent); outline-offset: -2px; z-index: 51; background: rgba(59, 130, 246, 0.03); }
    
    /* Drag & Drop Visuals */
    .node-children { min-height: 50px; border: 1px dashed rgba(0,0,0,0.05); padding: 10px; border-radius: 4px; }
    .node-children:empty::before { content: 'Arrastra elementos aquí'; display: block; padding: 20px; text-align: center; font-size: 12px; color: #94a3b8; font-style: italic; }
    
    /* Overlay to protect interaction while in builder */
    .live-preview-overlay { position: absolute; top: 0; left: 0; right: 0; bottom: 0; z-index: 10; cursor: pointer; }
    
    .node-tools { 
        position: absolute; top: -24px; left: 0; background: var(--accent); color: white; 
        padding: 2px 8px; border-radius: 4px 4px 0 0; font-size: 9px; text-transform: uppercase;
        display: none; align-items: center; gap: 8px; z-index: 100;
    }
    .node-item:hover > .node-tools { display: flex; }
    .tool-btn { cursor: pointer; opacity: 0.8; }
    .tool-btn:hover { opacity: 1; }
    
    /* Generic Canvas Fixes */
    .node-item img { max-width: 100%; height: auto; }
    .node-item .row { margin: 0; } /* Prevent horizontal scroll in builder */
    .node-item.col-md-6, .node-item.col-md-4, .node-item.col-md-3, .node-item.col-md-12, .node-item.col-md-8 { padding: 5px; }


    /* Properties UI */
    .prop-tabs { display: flex; border-bottom: 1px solid var(--border); margin-bottom: 20px; }
    .prop-tab { flex: 1; padding: 10px; text-align: center; font-size: 12px; cursor: pointer; color: #94a3b8; border-bottom: 2px solid transparent; }
    .prop-tab.active { color: var(--accent); border-bottom-color: var(--accent); }
    .prop-section-title { font-size: 12px; font-weight: 600; color: #cbd5e1; margin: 15px 0 10px; }
    .prop-row { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 10px; }
    .prop-input { background: #0f172a; border: 1px solid var(--border); color: white; padding: 6px 10px; border-radius: 4px; font-size: 12px; width: 100%; }
    .prop-input:focus { border-color: var(--accent); outline: none; }
    .prop-label { font-size: 11px; color: #64748b; margin-bottom: 4px; display: block; }

    /* Empty Space */
    .canvas-placeholder { 
        height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; 
        color: #94a3b8; gap: 1rem; padding: 100px 0;
    }

    /* Animation Preview Helper */
    .animate-bounce-in { animation: bounceIn 0.5s; }
</style>

<div class="app-shell">
    <!-- Header -->
    <header class="top-bar">
        <div class="d-flex align-items-center gap-3">
            <h5 class="mb-0 fw-bold"><i class="fa fa-pencil-ruler text-primary me-2"></i> OmniBuilder <span class="badge bg-primary fs-small">PRO</span></h5>
            <div class="ms-4 border-start ps-4 d-flex gap-2">
                <input type="text" id="pageTitle" class="prop-input" style="width: 200px;" placeholder="Nombre de página">
                <div class="device-toggles">
                    <button class="device-btn active" onclick="setDevice('desktop')" title="Desktop"><i class="fa fa-desktop"></i></button>
                    <button class="device-btn" onclick="setDevice('tablet')" title="Tablet"><i class="fa fa-tablet-alt"></i></button>
                    <button class="device-btn" onclick="setDevice('mobile')" title="Mobile"><i class="fa fa-mobile-alt"></i></button>
                </div>
            </div>
        </div>
        <div class="d-flex gap-2 align-items-center">
            <button class="btn btn-outline-secondary btn-sm" onclick="showPageList()"><i class="fa fa-folder-open"></i> Abrir</button>
            <button class="btn btn-outline-secondary btn-sm" onclick="previewPage()"><i class="fa fa-eye"></i> Previsualizar</button>
            <button class="btn btn-primary btn-sm px-4" id="btnSavePage"><i class="fa fa-cloud-upload-alt"></i> PUBLICAR</button>
        </div>
    </header>

    <main class="main-grid">
        <!-- Palette -->
        <aside class="sidebar-left custom-scrollbar">
            <div class="palette-group" id="layoutPalette">
                <div class="palette-title">Diseño</div>
                <div class="palette-item" draggable="true" data-type="container"><i class="fa fa-box"></i> Contenedor</div>
                <div class="palette-item" draggable="true" data-type="row"><i class="fa fa-columns"></i> Fila (Grid)</div>
                <div class="palette-item" draggable="true" data-type="column"><i class="fa fa-grip-lines"></i> Columna</div>
            </div>

            <div class="palette-group" id="customPalette">
                <div class="palette-title">Componentes</div>
                <div id="dynamicPaletteContent">
                     <div class="text-center opacity-50 py-3 small">Cargando...</div>
                </div>
            </div>

            <div class="px-3 mt-4">
                 <button class="btn btn-outline-primary btn-sm w-100" onclick="showComponentManager()">
                    <i class="fa fa-plus-circle me-1"></i> NUEVO BLOQUE
                 </button>
            </div>
        </aside>

        <!-- Builder -->
        <section class="workspace custom-scrollbar" onclick="selectNode(null)">
            <div id="canvasWrapper">
                <div id="mainCanvas" class="h-100 p-4" style="color: #1e293b;">
                    <div class="canvas-placeholder">
                        <i class="fa fa-layer-group fa-4x opacity-10"></i>
                        <p>Inicia tu diseño arrastrando elementos aquí</p>
                    </div>
                </div>
            </div>
            <div class="mt-3 text-muted small">ID de Página: <?= htmlspecialchars($pageId ?? 'Nueva') ?> | Slug: <span id="slugPreview">/url-automatica</span></div>
        </section>

        <!-- Properties -->
        <aside class="sidebar-right custom-scrollbar">
            <div class="prop-tabs">
                <div class="prop-tab active" data-tab="content">Contenido</div>
                <div class="prop-tab" data-tab="style">Estilo</div>
                <div class="prop-tab" data-tab="advanced">Avanzado</div>
                <div class="prop-tab" data-tab="html">HTML</div>
            </div>
            
            <div id="propertiesContent">
                <div class="text-center opacity-50 mt-5">
                    <i class="fa fa-mouse-pointer fa-3x mb-3"></i>
                    <p class="small">Selecciona un elemento para configurarlo</p>
                </div>
            </div>
        </aside>
    </main>
</div>

<!-- Component Manager Modal -->
<div class="modal fade" id="compManagerModal" tabindex="-1" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.8); z-index:9999; align-items:center; justify-content:center;">
    <div class="modal-content" style="background:var(--sidebar-bg); padding:30px; border-radius:8px; width:700px; max-height:90vh; border:1px solid var(--border);">
        <h5 id="compModalTitle">Nuevo Componente</h5>
        <input type="hidden" id="compId">
        
        <div class="row mt-3">
            <div class="col-md-6">
                <label class="prop-label">Nombre del Bloque</label>
                <input type="text" id="compName" class="prop-input mb-3" placeholder="Ej: Tarjeta de Producto">
            </div>
            <div class="col-md-6">
                <label class="prop-label">Icono (FontAwesome)</label>
                <input type="text" id="compIcon" class="prop-input mb-3" value="fa-cube" placeholder="fa-star, fa-heart...">
            </div>
            <div class="col-md-6">
                <label class="prop-label">Categoría</label>
                <input type="text" id="compCategory" class="prop-input mb-3" value="Mis Bloques">
            </div>
            <div class="col-md-6">
                <label class="prop-label">Etiqueta Base (Tag)</label>
                <input type="text" id="compTag" class="prop-input mb-3" value="div">
            </div>
            <div class="col-md-12">
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="compIsContainer">
                    <label class="form-check-label text-white-50" for="compIsContainer">Es un contenedor (permite arrastrar elementos dentro)</label>
                </div>
            </div>
        </div>

        <label class="prop-label">Código HTML / PHP</label>
        <textarea id="compHtml" class="prop-input" rows="8" style="font-family: monospace;"></textarea>

        <div class="mt-4 d-flex justify-content-between">
            <button class="btn btn-danger btn-sm" id="btnDeleteComp" style="display:none;" onclick="deleteComponentCurrent()">Eliminar</button>
            <div class="ms-auto">
                <button class="btn btn-secondary btn-sm" onclick="document.getElementById('compManagerModal').style.display='none'">Cancelar</button>
                <button class="btn btn-primary btn-sm ms-2" onclick="saveComponent()">Guardar Bloque</button>
            </div>
        </div>
    </div>
</div>

<!-- Page List Modal -->
<div class="modal fade" id="pageListModal" tabindex="-1" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.8); z-index:9999; align-items:center; justify-content:center;">
    <div class="modal-content" style="background:var(--sidebar-bg); padding:30px; border-radius:8px; width:600px; max-height:80vh; overflow-y:auto; border:1px solid var(--border);">
        <div class="d-flex justify-content-between mb-4">
            <h5 class="mb-0">Mis Páginas Dinámicas</h5>
            <button onclick="document.getElementById('pageListModal').style.display='none'" class="btn-close btn-close-white"></button>
        </div>
        <div id="pageListContainer">
            <div class="text-center py-5"><i class="fa fa-sync fa-spin fa-2x"></i></div>
        </div>
        <div class="mt-4 text-end">
            <button class="btn btn-secondary btn-sm" onclick="document.getElementById('pageListModal').style.display='none'">Cerrar</button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    let state = {
        selectedId: null,
        nodes: [],
        currentTab: 'content',
        pageId: '<?= $pageId ?>',
        pageTitle: '',
        pageSlug: '',
        accessLevel: 'auth',
        nextId: 1,
        customComponents: []
    };

    // --- Core Logic ---

    function setDevice(device) {
        const wrapper = document.getElementById('canvasWrapper');
        const btns = document.querySelectorAll('.device-btn');
        btns.forEach(b => b.classList.remove('active'));
        event.currentTarget.classList.add('active');

        if(device === 'desktop') wrapper.style.width = '100%';
        if(device === 'tablet') wrapper.style.width = '768px';
        if(device === 'mobile') wrapper.style.width = '375px';
    }

    // Initialize Sortable on each Palette Group
    document.querySelectorAll('.palette-group').forEach(group => {
        new Sortable(group, {
            group: {
                name: 'shared',
                pull: 'clone',
                put: false
            },
            sort: false,
            animation: 150,
            draggable: '.palette-item'
        });
    });

    // Initialize Sortable on Canvas (Recursive)
    function initSortable(el) {
        new Sortable(el, {
            group: 'shared',
            animation: 150,
            fallbackOnBody: true,
            swapThreshold: 0.65,
            ghostClass: 'bg-primary-subtle',
            onAdd: function (evt) {
                const type = evt.item.dataset.type;
                const name = evt.item.dataset.name;
                const newNode = createNode(type, name);
                evt.item.replaceWith(newNode);
                selectNode(newNode);
            }
        });
    }

    initSortable(document.getElementById('mainCanvas'));

    function createNode(type, name) {
        const placeholder = document.querySelector('.canvas-placeholder');
        if(placeholder) placeholder.remove();

        const id = 'node_' + state.nextId++;
        const el = document.createElement('div');
        el.id = id;
        el.dataset.type = type;
        
        const props = { tagName: 'div', attributes: {}, styles: {}, customClass: '', text: '' };

        const defs = {
            container: { tagName: 'div', class: 'container-fluid py-2', isContainer: true },
            row: { tagName: 'div', class: 'row', isContainer: true },
            column: { tagName: 'div', class: 'col-md-6', isContainer: true, attr: { width: 6 } }
        };

        let def = defs[type];
        
        // Check custom component
        if (!def) {
            const comp = state.customComponents.find(c => c.id === type || c.name === type);
            if (comp) {
                def = {
                    tagName: comp.tag_name,
                    text: comp.html_content,
                    isContainer: !!comp.is_container,
                    customId: comp.id
                };
            }
        }

        if(!def) def = { tagName: 'div' };
        props.tagName = def.tagName;
        props.text = def.text || '';
        props.customClass = def.class || '';
        if(def.attr) props.attributes = { ...def.attr };
        if(name) el.dataset.widget = name;

        // Apply grid classes to the builder node itself if it's a column
        el.className = 'node-item';
        if(type === 'column') el.className += ' col-md-' + props.attributes.width;

        let contentHtml = '';
        if(def.isContainer) {
            let containerClass = 'node-children';
            if(type === 'row') containerClass += ' row'; // Crucial for Bootstrap grid in builder
            contentHtml = `<div class="${containerClass}"></div>`;
        } else {
            // Live Preview of the real tag
            let attrStr = '';
            for(let key in props.attributes) attrStr += ` ${key}="${props.attributes[key]}"`;
            
            const selfClosing = ['img', 'hr', 'input'].includes(props.tagName);
            if(selfClosing) {
                contentHtml = `<${props.tagName} class="${props.customClass}" ${attrStr} />`;
            } else {
                contentHtml = `<${props.tagName} class="${props.customClass}" ${attrStr}>${props.text}</${props.tagName}>`;
            }
        }

        el.innerHTML = `
            <div class="node-tools">
                <span><i class="fa fa-code small me-1"></i>${props.tagName}</span>
                <i class="fa fa-copy tool-btn" onclick="duplicateNode('${id}')"></i>
                <i class="fa fa-trash tool-btn" onclick="deleteNode('${id}')"></i>
            </div>
            <div class="node-content-canvas">${contentHtml}</div>
            <div class="live-preview-overlay"></div>
        `;

        el.onclick = (e) => { e.stopPropagation(); selectNode(el); };
        el._props = props;

        if(def.isContainer) initSortable(el.querySelector('.node-children'));

        return el;
    }

    function syncCanvasView(el) {
        const props = el._props;
        const canvasContent = el.querySelector('.node-content-canvas');
        const preview = canvasContent.firstElementChild;

        // Update grid class if it's a column
        if(el.dataset.type === 'column') {
            el.className = 'node-item col-md-' + props.attributes.width;
        }

        if(preview && !preview.classList.contains('node-children')) {
            // Update attributes
            for(let key in props.attributes) {
                preview.setAttribute(key, props.attributes[key]);
            }
            // Update Text
            if(!['img', 'hr', 'input'].includes(props.tagName)) {
                preview.innerHTML = props.text;
            }
            // Update Classes & Styles
            preview.className = props.customClass;
            Object.assign(preview.style, props.styles);
        }
    }

    function selectNode(el) {
        document.querySelectorAll('.node-item').forEach(n => n.classList.remove('selected'));
        if(!el) {
            state.selectedId = null;
            renderProperties();
            return;
        }
        state.selectedId = el.id;
        el.classList.add('selected');
        renderProperties();
    }

    // --- Component Manager Logic ---
    async function loadPalette() {
        try {
            const res = await fetch('<?= url("/platform/builder/components") ?>');
            const data = await res.json();
            if(data.success) {
                state.customComponents = data.components;
                renderPalette();
            }
        } catch(e) { console.error("Error loading palette", e); }
    }

    function renderPalette() {
        const container = document.getElementById('dynamicPaletteContent');
        let html = '';
        state.customComponents.forEach(comp => {
            html += `
                <div class="palette-item" draggable="true" data-type="${comp.id}">
                    <i class="fa ${comp.icon}"></i> ${comp.name}
                </div>
            `;
        });
        if(state.customComponents.length === 0) html = '<div class="text-center opacity-50 py-3 small">No hay bloques aún</div>';
        container.innerHTML = html;
        
        // Re-init sortable on the new palette items
        new Sortable(container, {
            group: { name: 'shared', pull: 'clone', put: false },
            sort: false,
            animation: 150,
            draggable: '.palette-item'
        });
    }

    window.showComponentManager = (id = null) => {
        const modal = document.getElementById('compManagerModal');
        modal.style.display = 'flex';
        
        if(!id) {
            document.getElementById('compModalTitle').innerText = 'Nuevo Bloque Personalizado';
            document.getElementById('compId').value = '';
            document.getElementById('compName').value = '';
            document.getElementById('compIcon').value = 'fa-cube';
            document.getElementById('compCategory').value = 'Mis Bloques';
            document.getElementById('compTag').value = 'div';
            document.getElementById('compHtml').value = '';
            document.getElementById('compIsContainer').checked = false;
            document.getElementById('btnDeleteComp').style.display = 'none';
        } else {
            const comp = state.customComponents.find(c => c.id === id);
            if(comp) {
                document.getElementById('compModalTitle').innerText = 'Editar Bloque';
                document.getElementById('compId').value = comp.id;
                document.getElementById('compName').value = comp.name;
                document.getElementById('compIcon').value = comp.icon;
                document.getElementById('compCategory').value = comp.category;
                document.getElementById('compTag').value = comp.tag_name;
                document.getElementById('compHtml').value = comp.html_content;
                document.getElementById('compIsContainer').checked = !!comp.is_container;
                document.getElementById('btnDeleteComp').style.display = 'inline-block';
            }
        }
    }

    window.saveComponent = async () => {
        const payload = {
            id: document.getElementById('compId').value || null,
            name: document.getElementById('compName').value,
            icon: document.getElementById('compIcon').value,
            category: document.getElementById('compCategory').value,
            tag_name: document.getElementById('compTag').value,
            html_content: document.getElementById('compHtml').value,
            is_container: document.getElementById('compIsContainer').checked ? 1 : 0
        };

        if(!payload.name) return alert('Ponle un nombre al bloque');

        try {
            const res = await fetch('<?= url("/platform/builder/components/save") ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            if(data.success) {
                document.getElementById('compManagerModal').style.display = 'none';
                loadPalette();
                alert('¡Bloque guardado y listo para usar!');
            }
        } catch(e) { alert('Error al guardar el componente'); }
    }

    window.deleteComponentCurrent = async () => {
        const id = document.getElementById('compId').value;
        if(!id) return;
        if(!confirm('¿Seguro que quieres eliminar este bloque? Las páginas que lo usen podrían verse afectadas.')) return;

        try {
            const res = await fetch('<?= url("/platform/builder/components/delete") ?>?id=' + id, { method: 'POST' });
            const data = await res.json();
            if(data.success) {
                document.getElementById('compManagerModal').style.display = 'none';
                loadPalette();
            }
        } catch(e) { alert('Error al eliminar'); }
    }

    // --- Loading Logic ---
    async function showPageList() {
        const modal = document.getElementById('pageListModal');
        const container = document.getElementById('pageListContainer');
        modal.style.display = 'flex';
        container.innerHTML = '<div class="text-center py-5"><i class="fa fa-sync fa-spin fa-2x"></i></div>';

        try {
            const res = await fetch('<?= url("/platform/builder/pages") ?>');
            const data = await res.json();
            if(data.success) {
                let html = '<div class="list-group">';
                data.pages.forEach(p => {
                    html += `
                        <div class="list-group-item bg-dark text-white border-secondary d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">${p.title}</h6>
                                <small class="text-muted">${p.slug} | ${p.access_level}</small>
                            </div>
                            <button class="btn btn-primary btn-sm" onclick="loadPage('${p.id}')">Cargar</button>
                        </div>
                    `;
                });
                html += '</div>';
                if(data.pages.length === 0) html = '<p class="text-center opacity-50 py-4">No hay páginas creadas aún.</p>';
                container.innerHTML = html;
            }
        } catch(e) { container.innerHTML = '<p class="text-danger">Error al cargar la lista.</p>'; }
    }

    async function loadPage(id) {
        document.getElementById('pageListModal').style.display = 'none';
        try {
            const res = await fetch('<?= url("/platform/builder/load") ?>?id=' + id);
            const data = await res.json();
            if(data.success) {
                const page = data.page;
                state.pageId = page.id;
                state.pageTitle = page.title;
                state.pageSlug = page.slug;
                state.accessLevel = page.access_level;
                
                // Update UI Header
                document.getElementById('pageTitle').value = page.title;
                document.getElementById('slugPreview').innerText = page.slug;

                // Clear Canvas
                const canvas = document.getElementById('mainCanvas');
                canvas.innerHTML = '';
                
                // Reconstruct Nodes
                if(page.layout && page.layout.nodes) {
                    page.layout.nodes.forEach(nodeData => {
                        const nodeEl = reconstructNode(nodeData);
                        if(nodeEl) canvas.appendChild(nodeEl);
                    });
                }
                
                if(canvas.children.length === 0) {
                     canvas.innerHTML = '<div class="canvas-placeholder">...</div>'; // Restore if empty
                }

                renderProperties();
                alert('Página cargada con éxito.');
            }
        } catch(e) { alert('Error al cargar la página.'); }
    }

    function reconstructNode(nodeData) {
        const el = createNode(nodeData.type, nodeData.props.name);
        el._props = JSON.parse(JSON.stringify(nodeData.props));
        
        const childSlot = el.querySelector('.node-children');
        if(childSlot && nodeData.children) {
            nodeData.children.forEach(childData => {
                const childEl = reconstructNode(childData);
                if(childEl) childSlot.appendChild(childEl);
            });
        }
        
        syncCanvasView(el);
        return el;
    }

    function renderProperties() {
        const container = document.getElementById('propertiesContent');
        if(!state.selectedId) {
            // Global Page Settings
            let html = `
                <div class="prop-section-title">Ajustes de Página</div>
                <label class="prop-label">Título de Página</label>
                <input type="text" class="prop-input mb-3" oninput="updatePageState('pageTitle', this.value)" value="${state.pageTitle}">
                
                <label class="prop-label">URL (Slug)</label>
                <input type="text" class="prop-input mb-3" oninput="updatePageState('pageSlug', this.value)" value="${state.pageSlug}" placeholder="/ejemplo">
                
                <label class="prop-label">Nivel de Acceso</label>
                <select class="prop-input mb-3" onchange="updatePageState('accessLevel', this.value)">
                    <option value="public" ${state.accessLevel === 'public' ? 'selected' : ''}>Público</option>
                    <option value="auth" ${state.accessLevel === 'auth' ? 'selected' : ''}>Autenticado (Usuario)</option>
                    <option value="admin" ${state.accessLevel === 'admin' ? 'selected' : ''}>Admin</option>
                    <option value="super_admin" ${state.accessLevel === 'super_admin' ? 'selected' : ''}>Super Admin</option>
                </select>
                
                <hr class="opacity-10 my-4">
                <div class="alert alert-info py-2" style="font-size: 10px; background:rgba(59,130,246,0.1); border:none; color:#94a3b8;">
                    <i class="fa fa-info-circle me-1"></i> Estos ajustes afectan a la página completa. El slug se usa para la URL final.
                </div>
            `;
            container.innerHTML = html;
            return;
        }

        const el = document.getElementById(state.selectedId);
        if(!el || !el._props) {
            selectNode(null);
            return;
        }
        
        const props = el._props;
        const tag = props.tagName;

        let html = '';

        if(state.currentTab === 'content') {
            html += `<div class="prop-section-title">Contenido (${tag})</div>`;
            
            // Text Content (If not self-closing or certain tags)
            if(!['hr', 'img', 'input', 'br', 'ul', 'ol', 'table'].includes(tag)) {
                html += `
                    <label class="prop-label">Texto / HTML Interno</label>
                    <textarea class="prop-input mb-3" rows="3" oninput="updateProp('text', this.value)">${props.text}</textarea>
                `;
            }

            // Specific Attributes based on TAG
            if(tag === 'img') {
                html += `
                    <label class="prop-label">URL de Imagen (src)</label>
                    <input type="text" class="prop-input mb-2" oninput="updateAttr('src', this.value)" value="${props.attributes.src || ''}">
                    <label class="prop-label">Texto Alternativo (alt)</label>
                    <input type="text" class="prop-input mb-3" oninput="updateAttr('alt', this.value)" value="${props.attributes.alt || ''}">
                `;
            }
            if(tag === 'a' || tag === 'button') {
                html += `
                    <label class="prop-label">Enlace / Destino (href)</label>
                    <input type="text" class="prop-input mb-3" oninput="updateAttr('href', this.value)" value="${props.attributes.href || ''}">
                `;
            }
            if(tag === 'input') {
                html += `
                    <label class="prop-label">Placeholder</label>
                    <input type="text" class="prop-input mb-2" oninput="updateAttr('placeholder', this.value)" value="${props.attributes.placeholder || ''}">
                    <label class="prop-label">Tipo de entrada</label>
                    <select class="prop-input mb-3" onchange="updateAttr('type', this.value)">
                        <option value="text" ${props.attributes.type === 'text' ? 'selected' : ''}>Texto</option>
                        <option value="number" ${props.attributes.type === 'number' ? 'selected' : ''}>Número</option>
                        <option value="email" ${props.attributes.type === 'email' ? 'selected' : ''}>Email</option>
                        <option value="password" ${props.attributes.type === 'password' ? 'selected' : ''}>Password</option>
                        <option value="date" ${props.attributes.type === 'date' ? 'selected' : ''}>Fecha</option>
                    </select>
                `;
            }
            if(tag === 'div' && el.dataset.type === 'column') {
                html += `
                    <label class="prop-label">Columnas (1-12)</label>
                    <input type="number" class="prop-input mb-3" min="1" max="12" oninput="updateAttr('width', this.value)" value="${props.attributes.width || 6}">
                `;
            }

        } else if(state.currentTab === 'style') {
            html += `<div class="prop-section-title">Dimensiones y Layout</div>`;
            
            // Display Mode
            html += `
                <label class="prop-label">Modo de Visualización (Display)</label>
                <select class="prop-input mb-3" onchange="updateStyle('display', this.value)">
                    <option value="block" ${props.styles.display === 'block' ? 'selected' : ''}>Block (100% ancho)</option>
                    <option value="inline-block" ${props.styles['display'] === 'inline-block' ? 'selected' : ''}>Inline-Block (Contenido)</option>
                    <option value="flex" ${props.styles.display === 'flex' ? 'selected' : ''}>Flexbox (Contenedor)</option>
                    <option value="none" ${props.styles.display === 'none' ? 'selected' : ''}>Oculto</option>
                </select>
            `;

            // Flexbox Tools (Only if display is flex)
            if (props.styles.display === 'flex') {
                html += `
                    <div class="prop-row mb-3">
                        <div>
                            <label class="prop-label">Dirección</label>
                            <select class="prop-input" onchange="updateStyle('flex-direction', this.value)">
                                <option value="row" ${props.styles['flex-direction'] === 'row' ? 'selected' : ''}>Horizontal</option>
                                <option value="column" ${props.styles['flex-direction'] === 'column' ? 'selected' : ''}>Vertical</option>
                            </select>
                        </div>
                        <div>
                            <label class="prop-label">Alineación</label>
                            <select class="prop-input" onchange="updateStyle('justify-content', this.value)">
                                <option value="flex-start" ${props.styles['justify-content'] === 'flex-start' ? 'selected' : ''}>Inicio</option>
                                <option value="center" ${props.styles['justify-content'] === 'center' ? 'selected' : ''}>Centro</option>
                                <option value="flex-end" ${props.styles['justify-content'] === 'flex-end' ? 'selected' : ''}>Fin</option>
                                <option value="space-between" ${props.styles['justify-content'] === 'space-between' ? 'selected' : ''}>Espaciado</option>
                            </select>
                        </div>
                    </div>
                `;
            }

            // Dimensions Workspace
            const dims = ['width', 'height', 'max-width'];
            dims.forEach(dim => {
                const current = props.styles[dim] || 'auto';
                const val = current.match(/\d+/) ? current.match(/\d+/)[0] : '';
                const unit = current.match(/[a-z%]+/) ? current.match(/[a-z%]+/)[0] : 'auto';

                html += `
                    <label class="prop-label">${dim.toUpperCase()}</label>
                    <div class="prop-row mb-2">
                        <input type="text" class="prop-input" placeholder="Valor" 
                            oninput="updateStyleUnit('${dim}', this.value)" value="${val}">
                        <select class="prop-input" onchange="updateStyleUnit('${dim}', null, this.value)">
                            <option value="auto" ${unit === 'auto' ? 'selected' : ''}>Auto</option>
                            <option value="px" ${unit === 'px' ? 'selected' : ''}>px</option>
                            <option value="%" ${unit === '%' ? 'selected' : ''}>%</option>
                            <option value="vh" ${unit === 'vh' ? 'selected' : ''}>vh</option>
                            <option value="vw" ${unit === 'vw' ? 'selected' : ''}>vw</option>
                        </select>
                    </div>
                `;
            });

            html += `<div class="prop-section-title">Espaciado y Colores</div>`;
            html += `
                <div class="prop-row">
                    <div>
                        <label class="prop-label">Padding (px)</label>
                        <input type="number" class="prop-input" oninput="updateStyle('padding', this.value+'px')" value="${parseInt(props.styles.padding) || ''}">
                    </div>
                    <div>
                        <label class="prop-label">Margin Bottom</label>
                        <input type="number" class="prop-input" oninput="updateStyle('margin-bottom', this.value+'px')" value="${parseInt(props.styles['margin-bottom']) || ''}">
                    </div>
                </div>
                <label class="prop-label">Color de Fondo</label>
                <input type="color" class="prop-input mb-3" oninput="updateStyle('background-color', this.value)" value="${props.styles['background-color'] || '#ffffff'}">
                
                <label class="prop-label">Color de Texto</label>
                <input type="color" class="prop-input mb-3" oninput="updateStyle('color', this.value)" value="${props.styles['color'] || '#000000'}">
            `;
            html += `
                <label class="prop-label">Clases CSS Personalizadas</label>
                <input type="text" class="prop-input mb-3" oninput="updateProp('customClass', this.value)" value="${props.customClass}">
                <label class="prop-label">ID del Elemento (IDM)</label>
                <input type="text" class="prop-input" value="${el.id}" readonly>
            `;
        } else if(state.currentTab === 'html') {
            html += `<div class="prop-section-title">Editor de Código HTML</div>`;
            html += `
                <label class="prop-label">Etiqueta HTML (Tag)</label>
                <input type="text" id="htmlTagInput" class="prop-input mb-3" value="${props.tagName}" placeholder="div, span, section...">
                
                <label class="prop-label">Contenido HTML Interno</label>
                <textarea id="htmlContentInput" class="prop-input" rows="8" style="font-family: monospace;">${props.text}</textarea>
                <p class="text-muted small mt-2">Puedes usar etiquetas HTML aquí.</p>

                <button class="btn btn-primary btn-sm w-100 mt-3" onclick="applyHtmlChanges()">
                    <i class="fa fa-sync me-2"></i> ACTUALIZAR RENDERIZADO
                </button>
            `;
        }

        container.innerHTML = html;
    }

    // --- Update Logic ---

    window.updateProp = (key, val) => {
        if(!state.selectedId) return;
        const el = document.getElementById(state.selectedId);
        if(el && el._props) {
            el._props[key] = val;
            syncCanvasView(el);
        }
    };

    window.updateAttr = (key, val) => {
        if(!state.selectedId) return;
        const el = document.getElementById(state.selectedId);
        if(el && el._props) {
            el._props.attributes[key] = val;
            syncCanvasView(el);
        }
    };

    window.updateStyle = (key, val) => {
        if(!state.selectedId) return;
        const el = document.getElementById(state.selectedId);
        if(el && el._props) {
            el._props.styles[key] = val;
            syncCanvasView(el);
        }
    };

    window.applyHtmlChanges = () => {
        if(!state.selectedId) return;
        const el = document.getElementById(state.selectedId);
        const tag = document.getElementById('htmlTagInput').value;
        const content = document.getElementById('htmlContentInput').value;

        if(el && el._props) {
            el._props.tagName = tag;
            el._props.text = content;
            syncCanvasView(el);
        }
    }

    window.updatePageState = (key, val) => {
        state[key] = val;
        if(key === 'pageTitle') {
            document.getElementById('pageTitle').value = val;
            const slug = val.toLowerCase().replace(/ /g, '-').replace(/[^\w-/]+/g, ''); 
            if(!state.pageSlug || state.pageSlug === '') {
                 state.pageSlug = slug;
                 document.getElementById('slugPreview').innerText = '/' + slug;
            }
        }
        if(key === 'pageSlug') {
            document.getElementById('slugPreview').innerText = '/' + val;
        }
    }

    window.updateStyleUnit = (key, val, unit) => {
        if(!state.selectedId) return;
        const el = document.getElementById(state.selectedId);
        if(!el || !el._props) return;

        const current = el._props.styles[key] || 'auto';
        let currentVal = current.match(/\d+/) ? current.match(/\d+/)[0] : '';
        let currentUnit = current.match(/[a-z%]+/) ? current.match(/[a-z%]+/)[0] : 'auto';

        if (val !== null) currentVal = val;
        if (unit !== undefined) currentUnit = unit;

        const finalVal = currentUnit === 'auto' ? 'auto' : (currentVal + currentUnit);
        el._props.styles[key] = finalVal;
        syncCanvasView(el);
    };

    function syncCanvasView(el) {
        if(!el || !el._props) return;
        const props = el._props;
        const canvasContent = el.querySelector('.node-content-canvas');
        if(!canvasContent) return;
        
        let preview = canvasContent.firstElementChild;
        const isContainer = preview && preview.classList.contains('node-children');

        // Apply Layout Styles to the Outer Wrapper (the .node-item) 
        const layoutKeys = ['display', 'width', 'height', 'max-width', 'flex', 'flex-direction', 'justify-content', 'align-items'];
        layoutKeys.forEach(k => {
            if (props.styles[k]) {
                el.style[k] = props.styles[k];
            } else {
                el.style[k] = ''; // Reset
            }
        });

        // Column handling
        if(el.dataset.type === 'column') {
            el.className = 'node-item col-md-' + props.attributes.width;
        }

        // --- Tag Switching Logic ---
        if (preview && preview.tagName.toLowerCase() !== props.tagName.toLowerCase()) {
            const newPreview = document.createElement(props.tagName);
            
            // Transfer properties
            if (isContainer) {
                newPreview.className = preview.className; // Maintain .node-children and maybe .row
                // Move children
                while (preview.firstChild) newPreview.appendChild(preview.firstChild);
            }
            
            preview.replaceWith(newPreview);
            preview = newPreview;
        }

        if(preview) {
            // Update attributes
            for(let key in props.attributes) {
                preview.setAttribute(key, props.attributes[key]);
            }
            
            // Update inner content (Always if not self-closing)
            if(!['img', 'hr', 'input'].includes(props.tagName.toLowerCase())) {
                // If it was a container but we have raw text, the text takes over
                if (props.text || !isContainer) {
                    preview.innerHTML = props.text;
                }
            }
            
            // Update Classes & Styles
            preview.className = props.customClass + (isContainer ? ' node-children' : '');
            if(el.dataset.type === 'row' && isContainer) preview.className += ' row';
            
            Object.assign(preview.style, props.styles);
            
            if (props.styles.display === 'inline-block' || props.styles.width) {
                 preview.style.width = '100%';
            }
        }
    }

    function deleteNode(id) {
        document.getElementById(id).remove();
        if(state.selectedId === id) selectNode(null);
    }

    function duplicateNode(id) {
        const original = document.getElementById(id);
        const newNode = cloneNodeRecursive(original);
        original.after(newNode);
        selectNode(newNode);
    }

    function cloneNodeRecursive(el) {
        if(!el || !el._props) return null;
        const clone = el.cloneNode(true);
        const newId = 'node_' + state.nextId++;
        clone.id = newId;
        clone._props = JSON.parse(JSON.stringify(el._props));
        
        // Re-setup listener
        clone.onclick = (e) => { e.stopPropagation(); selectNode(clone); };
        
        // Re-setup tools
        clone.querySelector('.fa-copy').onclick = () => duplicateNode(newId);
        clone.querySelector('.fa-trash').onclick = (e) => { e.stopPropagation(); deleteNode(newId); };

        // If it's a container, we must recursively re-init sortable and clone children props
        const childSlot = clone.querySelector('.node-children');
        if(childSlot) {
            initSortable(childSlot);
            const originalItems = el.querySelectorAll(':scope > .node-content-canvas > .node-children > .node-item');
            const clonedItems = clone.querySelectorAll(':scope > .node-content-canvas > .node-children > .node-item');
            
            clonedItems.forEach((child, index) => {
                const updatedChild = cloneNodeRecursive(originalItems[index]);
                if(updatedChild) child.replaceWith(updatedChild);
            });
        }
        
        return clone;
    }

    // --- Tabs Handling ---
    document.querySelectorAll('.prop-tab').forEach(tab => {
        tab.onclick = () => {
            document.querySelectorAll('.prop-tab').forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            state.currentTab = tab.dataset.tab;
            renderProperties();
        };
    });

    function serializeNodes(container) {
        const nodes = [];
        if(!container) return nodes;

        container.querySelectorAll(':scope > .node-item').forEach(el => {
            if(!el._props) return;
            const props = JSON.parse(JSON.stringify(el._props));
            const node = {
                type: el.dataset.type,
                props: props,
                children: []
            };

            const childSlot = el.querySelector('.node-children');
            if(childSlot) {
                node.children = serializeNodes(childSlot);
            }
            props.name = el.dataset.widget; 
            nodes.push(node);
        });
        return nodes;
    }

    // --- Save Logic ---
    document.getElementById('btnSavePage').onclick = async () => {
        const title = state.pageTitle || document.getElementById('pageTitle').value;
        const slug = state.pageSlug || title.toLowerCase().replace(/ /g, '-').replace(/[^\w-/]+/g, ''); 
        
        if(!title) return alert('Danos un título para tu obra maestra.');

        const nodes = serializeNodes(document.getElementById('mainCanvas'));

        const btn = document.getElementById('btnSavePage');
        btn.innerHTML = '<i class="fa fa-sync fa-spin"></i> PUBLICANDO...';
        
        try {
            const res = await fetch('<?= url("/platform/builder/save") ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ 
                    id: state.pageId, 
                    title: title, 
                    slug: slug, 
                    access_level: state.accessLevel,
                    layout: { nodes } 
                })
            });
            const data = await res.json();
            if(data.success) {
                alert('🚀 ¡Página publicada en la red! URL: /' + slug);
                document.getElementById('slugPreview').innerText = '/' + slug;
            } else alert('Error: ' + data.error);
        } catch(e) {
            alert('Error crítico de red.');
        } finally {
            btn.innerHTML = '<i class="fa fa-cloud-upload-alt"></i> PUBLICAR';
        }
    };

    // --- Final Polish: Auto-slug ---
    document.getElementById('pageTitle').oninput = (e) => {
        const title = e.target.value;
        const slug = title.toLowerCase().replace(/ /g, '-').replace(/[^\w-/]+/g, '');
        if(!state.pageSlug || state.pageSlug === '') {
            document.getElementById('slugPreview').innerText = '/' + (slug || 'url-automatica');
        }
    };

    function previewPage() {
        const title = document.getElementById('pageTitle').value;
        const slug = state.pageSlug || title.toLowerCase().replace(/ /g, '-').replace(/[^\w-/]+/g, '');
        
        if(!slug) return alert('Primero ponle un título a la página');
        
        // Ensure slug starts with something meaningful
        const cleanSlug = slug.startsWith('/') ? slug.substring(1) : slug;
        window.open('<?= url("/") ?>' + cleanSlug, '_blank');
    }

    // Initialize Components
    loadPalette();
</script>
