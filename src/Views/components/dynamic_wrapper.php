<div class="dynamic-page-wrapper">
    <!-- Header injected by layout -->
    
    <div class="dynamic-content fade-in">
        <?php 
        // Omnibuilder Pro: Execute embedded PHP code safely on the server
        eval("?> " . $dynamicContent); 
        ?>
    </div>
</div>
