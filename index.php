<?php require_once('./config.php'); ?>
<!DOCTYPE html>
<html lang="en" class="" style="height: auto;">
<style>
  #header {
    height: 70vh;
    width: calc(100%);
    position: relative;
    top: -1em;
  }

  #header:before {
    content: "";
    position: absolute;
    height: calc(100%);
    width: calc(100%);
    background-image: url(<?= validate_image($_settings->info("cover")) ?>);
    background-size: cover;
    background-repeat: no-repeat;
    background-position: center center;
    filter: brightness(0.85);
  }

  #header>div {
    position: absolute;
    height: calc(100%);
    width: calc(100%);
    z-index: 2;
  }

  #top-Nav a.nav-link.active {
    color: #fff;
    font-weight: 900;
    position: relative;
  }

  #top-Nav a.nav-link.active:before {
    content: "";
    position: absolute;
    border-bottom: 2px solid #fff;
    width: 33.33%;
    left: 33.33%;
    bottom: 0;
  }
</style>
<?php require_once('inc/header.php') ?>

<body class="layout-top-nav layout-fixed layout-navbar-fixed dark-mode" style="height: auto;">
  <div class="wrapper">
    <?php $page = isset($_GET['page']) ? $_GET['page'] : 'home';  ?>
    <?php require_once('inc/topBarNav.php') ?>
    <?php if ($_settings->chk_flashdata('success')) : ?>
      <script>
        alert_toast("<?php echo $_settings->flashdata('success') ?>", 'success')
      </script>
    <?php endif; ?>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper pt-5" style="">
      <?php if ($page == "home" || $page == "about_us") : ?>
        <div id="header" class="shadow mb-4">
          <div class="d-flex justify-content-center h-100 w-100 align-items-center flex-column px-3">
            <h1 class="w-100 text-center site-title px-5"><?php echo $_settings->info('name') ?></h1>
            <button class="btn btn-lg btn-primary rounded-pill px-4 w-25" id="book_now">Reservar ahora!</button>
          </div>
        </div>
      <?php endif; ?>
      <!-- Main content -->
      <section class="content ">
        <div class="container">
          <?php if ($_settings->chk_flashdata('block_success')) : ?>
            <div class="alert alert-success">
              <div class="w-100 d-flex align-items-end">
                <div class="col-11">
                  <p><?= $_settings->flashdata('block_success') ?></p>
                </div>
                <div class="col-1 text-right">
                  <button class="close text-dark" onclick="$(this).closest('.alert').remove()"><span aria-hidden="true">&times;</span></button>
                </div>
              </div>
            </div>
          <?php endif; ?>
          <?php
          if (!file_exists($page . ".php") && !is_dir($page)) {
            include '404.html';
          } else {
            if (is_dir($page))
              include $page . '/index.php';
            else
              include $page . '.php';
          }
          ?>
        </div>
      </section>
      <!-- /.content -->
      <div class="modal fade rounded-0" id="confirm_modal" role='dialog'>
        <div class="modal-dialog modal-md modal-dialog-centered" role="document">
          <div class="modal-content">
            <div class="modal-header rounded-0">
              <h5 class="modal-title">Confirmación</h5>
            </div>
            <div class="modal-body rounded-0">
              <div id="delete_content"></div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-primary" id='confirm' onclick="">Continuar</button>
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
          </div>
        </div>
      </div>
      <div class="modal fade rounded-0" id="uni_modal" role='dialog'>
        <div class="modal-dialog modal-md modal-dialog-centered rounded-0" role="document">
          <div class="modal-content rounded-0">
            <div class="modal-header rounded-0">
              <h5 class="modal-title"></h5>
            </div>
            <div class="modal-body rounded-0">
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-primary" id='submit' onclick="$('#uni_modal form').submit()">Guardar</button>
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            </div>
          </div>
        </div>
      </div>
      <div class="modal fade rounded-0" id="uni_modal_right" role='dialog'>
        <div class="modal-dialog modal-full-height  modal-md" role="document">
          <div class="modal-content">
            <div class="modal-header rounded-0">
              <h5 class="modal-title"></h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span class="fa fa-arrow-right"></span>
              </button>
            </div>
            <div class="modal-body rounded-0">
            </div>
          </div>
        </div>
      </div>
      <div class="modal fade" id="viewer_modal" role='dialog'>
        <div class="modal-dialog modal-md" role="document">
          <div class="modal-content">
            <button type="button" class="btn-close" data-dismiss="modal"><span class="fa fa-times"></span></button>
            <img src="" alt="">
          </div>
        </div>
      </div>
    </div>
    <!-- /.content-wrapper -->
    <?php require_once('inc/footer.php') ?>
    <script>
      $(function() {
        $('#book_now').click(function() {
          uni_modal("Formulario de Reserva", 'book_event.php', 'mid-large')
        })
      })
    </script>
</body>

</html>