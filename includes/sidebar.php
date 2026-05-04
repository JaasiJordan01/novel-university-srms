<div class="offcanvas offcanvas-start d-lg-none" tabindex="-1" id="sidebarMenu" aria-labelledby="sidebarMenuLabel">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title" id="sidebarMenuLabel">Menu</h5>
    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body p-0">
    <?php include __DIR__.'/sidebar_content.php'; ?>
  </div>
</div>

<!-- Desktop sidebar -->
<nav id="desktopSidebar" class="d-none d-lg-block bg-success text-white sidebar" style="width: 250px; position: fixed; left: 0; top: 56px; height: calc(100vh - 56px); overflow-y: auto;">
  <?php include __DIR__.'/sidebar_content.php'; ?>
</nav>