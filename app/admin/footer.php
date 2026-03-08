  <!-- /.content-wrapper -->
  <footer class="main-footer">
    <div class="pull-right hidden-xs">
      <b>Version</b> <?php 
        // Ambil versi dari pengaturan aplikasi
        if(isset($app_settings['app_version'])) {
          echo htmlspecialchars($app_settings['app_version']);
        } else {
          echo '1.0.0';
        }
      ?>
    </div>
    <strong>Copyright &copy; <?php echo date('Y') ?></strong> - <?php 
      // Ambil nama aplikasi dan institusi dari pengaturan
      $app_name = isset($app_settings['app_name']) ? htmlspecialchars($app_settings['app_name']) : 'SISBK';
      $app_institution = isset($app_settings['app_author']) ? htmlspecialchars($app_settings['app_author']) : 'Madrasah Aliyah Yasmu';
      echo $app_name . ' ' . $app_institution;
    ?> | Dikembangkan oleh <strong>Wilade</strong>
  </footer>

  
</div>


<script src="../assets/bower_components/jquery/dist/jquery.min.js"></script>

<script src="../assets/bower_components/jquery-ui/jquery-ui.min.js"></script>

<script>
  $.widget.bridge('uibutton', $.ui.button);
</script>

<script src="../assets/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

<!-- <script src="../assets/bower_components/raphael/raphael.min.js"></script>
<script src="../assets/bower_components/morris.js/morris.min.js"></script> -->

<script src="../assets/bower_components/jquery-sparkline/dist/jquery.sparkline.min.js"></script>


<script src="../assets/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="../assets/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>

<!-- <script src="../assets/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
<script src="../assets/plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script> -->

<script src="../assets/bower_components/jquery-knob/dist/jquery.knob.min.js"></script>

<script src="../assets/bower_components/moment/min/moment.min.js"></script>
<script src="../assets/bower_components/bootstrap-daterangepicker/daterangepicker.js"></script>

<script src="../assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

<script src="../assets/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>

<script src="../assets/bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>

<script src="../assets/bower_components/fastclick/lib/fastclick.js"></script>

<script src="../assets/dist/js/adminlte.min.js"></script>

<!-- <script src="../assets/dist/js/pages/dashboard.js"></script> -->

<script src="../assets/dist/js/demo.js"></script>

<!-- Select2 CSS -->
<link rel="stylesheet" href="../assets/bower_components/select2/dist/css/select2.min.css">

<!-- Select2 JavaScript -->
<script src="../assets/bower_components/select2/dist/js/select2.min.js"></script>

<script>
// Ensure AdminLTE is properly initialized
$(document).ready(function(){
  // Wait for AdminLTE to be available
  setTimeout(function() {
    if (typeof $.AdminLTE !== 'undefined') {
      // AdminLTE is available, initialize treeview
      try {
        $('.sidebar-menu').tree();
      } catch(e) {
        // Treeview initialization failed, but continue
      }
    }
  }, 100);
  
  // Initialize Select2
  $('.select2').select2({
    placeholder: "Ketik untuk mencari...",
    allowClear: true,
    width: '100%'
  });
  
  // Initialize Bootstrap DatePicker
  $('.datepicker2').datepicker({
    format: 'yyyy-mm-dd',
    autoclose: true,
    todayHighlight: true,
    orientation: 'bottom auto'
  });
  
  // Initialize Bootstrap DatePicker for datepicker class (dd/mm/yyyy format)
  $('.datepicker').datepicker({
    format: 'dd/mm/yyyy',
    autoclose: true,
    todayHighlight: true,
    orientation: 'bottom auto'
  });
  
  // Auto-refresh notifikasi badge setiap 5 detik (untuk administrator dan guru BK)
  <?php if(isset($_SESSION['level']) && ($_SESSION['level'] == 'guru_bk' || $_SESSION['level'] == 'administrator')): ?>
  setInterval(function() {
    $.ajax({
      url: 'get_notif_count.php',
      method: 'GET',
      cache: false,
      dataType: 'json',
      success: function(data) {
        // Update badge untuk administrator (notifikasi RTL)
        <?php if(isset($_SESSION['level']) && $_SESSION['level'] == 'administrator'): ?>
        var $badge = $('.notifications-menu .label');
        if(data.total > 0) {
          if($badge.length > 0) {
            $badge.text(data.total);
          } else {
            $('.notifications-menu > a').append('<span class="label label-warning">' + data.total + '</span>');
          }
        } else {
          $badge.remove();
        }
        <?php else: ?>
        // Update badge untuk guru BK
        var $badge = $('#notif-bell .label');
        if(data.total > 0) {
          if($badge.length > 0) {
            $badge.text(data.total);
          } else {
            $('#notif-bell').append('<span class="label label-warning">' + data.total + '</span>');
          }
        } else {
          $badge.remove();
        }
        <?php endif; ?>
      }
    });
  }, 30000);
  
  // Update notifikasi segera setelah halaman dimuat
  setTimeout(function() {
    $.ajax({
      url: 'get_notif_count.php',
      method: 'GET',
      cache: false,
      dataType: 'json',
      success: function(data) {
        // Update badge untuk administrator (notifikasi RTL)
        <?php if(isset($_SESSION['level']) && $_SESSION['level'] == 'administrator'): ?>
        var $badge = $('.notifications-menu .label');
        if(data.total > 0) {
          if($badge.length > 0) {
            $badge.text(data.total);
          } else {
            $('.notifications-menu > a').append('<span class="label label-warning">' + data.total + '</span>');
          }
        } else {
          $badge.remove();
        }
        <?php else: ?>
        // Update badge untuk guru BK
        var $badge = $('#notif-bell .label');
        if(data.total > 0) {
          if($badge.length > 0) {
            $badge.text(data.total);
          } else {
            $('#notif-bell').append('<span class="label label-warning">' + data.total + '</span>');
          }
        } else {
          $badge.remove();
        }
        <?php endif; ?>
      }
    });
  }, 500);
  <?php endif; ?>
  
  // Function untuk menandai notifikasi sebagai sudah dibaca
  function markNotificationRead(notif_id) {
    if(notif_id > 0) {
      $.ajax({
        url: 'notifikasi_rtl_act.php',
        method: 'GET',
        data: {
          action: 'mark_read',
          id: notif_id
        },
        success: function(response) {
          // Refresh halaman setelah beberapa detik untuk update badge notifikasi
          setTimeout(function() {
            location.reload();
          }, 500);
        },
        error: function() {
          // Jika error, tetap lanjutkan navigasi
          console.log('Error marking notification as read');
        }
      });
    }
  }
});
</script>

<script src="../assets/bower_components/ckeditor/ckeditor.js"></script>

</body>
</html>
