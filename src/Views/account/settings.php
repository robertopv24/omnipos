<div class="settings-container" style="max-width: 800px; margin: 2rem auto;">
    <h1><?= __('settings') ?></h1>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success"
            style="background: rgba(16, 185, 129, 0.1); color: #10b981; padding: 1rem; border-radius: 8px; margin-bottom: 2rem; border: 1px solid rgba(16, 185, 129, 0.2);">
            Configuración actualizada con éxito.
        </div>
    <?php endif; ?>

    <div class="glass-effect" style="padding: 2.5rem; margin-top: 2rem;">
        <form action="<?= url('/account/settings/update') ?>" method="POST" enctype="multipart/form-data">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                <div class="form-group" style="grid-column: span 2; display: flex; gap: 2rem; margin-bottom: 2rem;">
                    <div style="flex: 1;">
                        <label style="display: block; margin-bottom: 0.5rem; color: #94a3b8;">Logo de la Plataforma
                            (Sidebar/Login)</label>
                        <?php if ($business['logo_path']): ?>
                            <img src="<?= url('/uploads/' . $business['logo_path']) ?>"
                                style="height: 60px; margin-bottom: 1rem; display: block; filter: drop-shadow(0 0 10px rgba(0,0,0,0.3));">
                        <?php endif; ?>
                        <input type="file" name="logo" style="width: 100%; color: #64748b;">
                    </div>
                    <div style="flex: 1;">
                        <label style="display: block; margin-bottom: 0.5rem; color: #94a3b8;">Logo para Clientes
                            (Facturas/Tickets)</label>
                        <?php if ($business['client_logo_path']): ?>
                            <img src="<?= url('/uploads/' . $business['client_logo_path']) ?>"
                                style="height: 60px; margin-bottom: 1rem; display: block;">
                        <?php endif; ?>
                        <input type="file" name="client_logo" style="width: 100%; color: #64748b;">
                    </div>
                </div>
                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; color: var(--text-dim);">País</label>
                    <select name="country"
                        style="width: 100%; padding: 0.85rem; border-radius: 8px; border: 1px solid var(--border-bright); background: var(--bg-card); color: var(--text-pure);">
                        <option value="VE" <?= $business['country'] == 'VE' ? 'selected' : '' ?>>Venezuela</option>
                        <option value="CO" <?= $business['country'] == 'CO' ? 'selected' : '' ?>>Colombia</option>
                        <option value="US" <?= $business['country'] == 'US' ? 'selected' : '' ?>>USA</option>
                        <option value="ES" <?= $business['country'] == 'ES' ? 'selected' : '' ?>>España</option>
                    </select>
                </div>

                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; color: var(--text-dim);">Idioma</label>
                    <select name="language"
                        style="width: 100%; padding: 0.85rem; border-radius: 8px; border: 1px solid var(--border-bright); background: var(--bg-card); color: var(--text-pure);">
                        <option value="es" <?= $business['language'] == 'es' ? 'selected' : '' ?>>Español</option>
                        <option value="en" <?= $business['language'] == 'en' ? 'selected' : '' ?>>English</option>
                    </select>
                </div>

                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; color: var(--text-dim);">Zona Horaria</label>
                    <select name="timezone"
                        style="width: 100%; padding: 0.85rem; border-radius: 8px; border: 1px solid var(--border-bright); background: var(--bg-card); color: var(--text-pure);">
                        <option value="America/Caracas" <?= $business['timezone'] == 'America/Caracas' ? 'selected' : '' ?>>America/Caracas</option>
                        <option value="America/Bogota" <?= $business['timezone'] == 'America/Bogota' ? 'selected' : '' ?>>
                            America/Bogota</option>
                        <option value="America/New_York" <?= $business['timezone'] == 'America/New_York' ? 'selected' : '' ?>>America/New_York</option>
                        <option value="UTC" <?= $business['timezone'] == 'UTC' ? 'selected' : '' ?>>UTC</option>
                    </select>
                </div>

                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; color: var(--text-dim);">Tema Visual</label>
                    <select name="theme"
                        style="width: 100%; padding: 0.85rem; border-radius: 8px; border: 1px solid var(--border-bright); background: var(--bg-card); color: var(--text-pure);">
                        <option value="default" <?= $business['theme'] == 'default' ? 'selected' : '' ?>>Claro (Default)
                        </option>
                        <option value="dark" <?= $business['theme'] == 'dark' ? 'selected' : '' ?>>Oscuro (Night)</option>
                        <option value="custom" <?= $business['theme'] == 'custom' ? 'selected' : '' ?>>Personalizado (Brand
                            Alpha)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; color: var(--text-dim);">Moneda Principal</label>
                    <input type="text" name="currency" value="<?= $business['currency'] ?>"
                        style="width: 100%; padding: 0.85rem; border-radius: 8px; border: 1px solid var(--border-bright); background: var(--bg-card); color: var(--text-pure);">
                </div>

                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; color: var(--text-dim);">ID Fiscal (RIF/NIT)</label>
                    <input type="text" name="tax_id" value="<?= $business['tax_id'] ?>"
                        style="width: 100%; padding: 0.85rem; border-radius: 8px; border: 1px solid var(--border-bright); background: var(--bg-card); color: var(--text-pure);">
                </div>

                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; color: #94a3b8;">Color Primario (Para Tema
                        Custom)</label>
                    <input type="color" name="primary_color"
                        value="<?= $business['theme_settings']['primary'] ?? '#3b82f6' ?>"
                        style="width: 100%; height: 45px; padding: 0.25rem; border-radius: 8px; border: 1px solid #334155; background: #1e293b; cursor: pointer;">
                </div>

                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; color: #94a3b8;">Color Secundario (Para Tema
                        Custom)</label>
                    <input type="color" name="secondary_color"
                        value="<?= $business['theme_settings']['secondary'] ?? '#1e293b' ?>"
                        style="width: 100%; height: 45px; padding: 0.25rem; border-radius: 8px; border: 1px solid #334155; background: #1e293b; cursor: pointer;">
                </div>
            </div>

            <div style="margin-top: 3rem; display: flex; gap: 1rem;">
                <button type="submit" class="btn btn-primary" style="flex: 2;">GUARDAR CAMBIOS</button>
                <a href="<?= url('/dashboard') ?>" class="btn"
                    style="flex: 1; background: #475569; color: white; padding: 0.85rem; text-decoration: none; border-radius: 8px; text-align: center;">Volver</a>
            </div>
        </form>
    </div>
</div>