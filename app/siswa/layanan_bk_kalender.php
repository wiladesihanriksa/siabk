<?php include 'header.php'; ?>

<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Kalender Layanan BK
      <small>Jadwal Layanan Bimbingan dan Konseling</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="#">Layanan BK</a></li>
      <li class="active">Kalender</li>
    </ol>
  </section>

  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title">
              <i class="fa fa-calendar"></i> Kalender Kegiatan Layanan BK
            </h3>
          </div>
          
          <div class="box-body">
            <div id="calendar"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Legend -->
    <div class="row">
      <div class="col-md-12">
        <div class="box box-default">
          <div class="box-header with-border">
            <h3 class="box-title">
              <i class="fa fa-info-circle"></i> Keterangan Warna
            </h3>
          </div>
          
          <div class="box-body">
            <div class="row">
              <div class="col-md-3">
                <div class="info-box">
                  <span class="info-box-icon bg-blue"><i class="fa fa-graduation-cap"></i></span>
                  <div class="info-box-content">
                    <span class="info-box-text">Layanan Klasikal</span>
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="info-box">
                  <span class="info-box-icon bg-green"><i class="fa fa-users"></i></span>
                  <div class="info-box-content">
                    <span class="info-box-text">Bimbingan Kelompok</span>
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="info-box">
                  <span class="info-box-icon bg-yellow"><i class="fa fa-heart"></i></span>
                  <div class="info-box-content">
                    <span class="info-box-text">Konseling Kelompok</span>
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="info-box">
                  <span class="info-box-icon bg-red"><i class="fa fa-comments"></i></span>
                  <div class="info-box-content">
                    <span class="info-box-text">Konsultasi</span>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="row">
              <div class="col-md-3">
                <div class="info-box">
                  <span class="info-box-icon bg-purple"><i class="fa fa-handshake-o"></i></span>
                  <div class="info-box-content">
                    <span class="info-box-text">Mediasi</span>
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="info-box">
                  <span class="info-box-icon bg-orange"><i class="fa fa-gavel"></i></span>
                  <div class="info-box-content">
                    <span class="info-box-text">Layanan Advokasi</span>
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="info-box">
                  <span class="info-box-icon bg-teal"><i class="fa fa-briefcase"></i></span>
                  <div class="info-box-content">
                    <span class="info-box-text">Layanan Peminatan</span>
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="info-box">
                  <span class="info-box-icon bg-gray"><i class="fa fa-ellipsis-h"></i></span>
                  <div class="info-box-content">
                    <span class="info-box-text">Lainnya</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Informasi Tambahan -->
    <div class="row">
      <div class="col-md-12">
        <div class="box box-info">
          <div class="box-header with-border">
            <h3 class="box-title">
              <i class="fa fa-info-circle"></i> Informasi Kalender
            </h3>
          </div>
          
          <div class="box-body">
            <div class="alert alert-info">
              <h4><i class="fa fa-info"></i> Cara Menggunakan Kalender</h4>
              <ul>
                <li><strong>Klik pada tanggal</strong> untuk melihat detail layanan BK pada tanggal tersebut</li>
                <li><strong>Gunakan navigasi</strong> (bulan/tahun) untuk melihat layanan BK pada periode tertentu</li>
                <li><strong>Warna berbeda</strong> menunjukkan jenis layanan BK yang berbeda</li>
                <li><strong>Layanan yang Anda ikuti</strong> akan ditandai khusus di halaman "Layanan BK Saya"</li>
              </ul>
              <p>Jika Anda ingin mengikuti layanan BK tertentu, silakan hubungi guru BK untuk informasi lebih lanjut.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<!-- Include FullCalendar CSS -->
<link rel="stylesheet" href="../assets/bower_components/fullcalendar/dist/fullcalendar.min.css">
<link rel="stylesheet" href="../assets/bower_components/fullcalendar/dist/fullcalendar.print.min.css" media="print">

<!-- Include FullCalendar JS -->
<script src="../assets/bower_components/moment/min/moment.min.js"></script>
<script src="../assets/bower_components/fullcalendar/dist/fullcalendar.min.js"></script>
<script src="../assets/bower_components/fullcalendar/dist/locale/id.js"></script>

<script>
$(document).ready(function() {
    // Initialize FullCalendar
    $('#calendar').fullCalendar({
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,agendaWeek,agendaDay'
        },
        defaultDate: new Date(),
        navLinks: true,
        editable: false,
        eventLimit: true,
        locale: 'id',
        events: {
            url: 'ajax_get_layanan_bk_siswa.php',
            error: function() {
                alert('Gagal memuat data layanan BK');
            }
        },
        eventClick: function(calEvent, jsEvent, view) {
            // Open detail modal or redirect to detail page
            window.open('layanan_bk_detail.php?id=' + calEvent.id, '_blank');
        },
        eventRender: function(event, element) {
            element.attr('title', event.title + '\n' + event.description);
            element.attr('data-toggle', 'tooltip');
        }
    });
    
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();
    
    // Pastikan sidebar tetap berfungsi
    $('.sidebar-menu').tree({
        animation: 200
    });
    
    // Pastikan treeview menu berfungsi
    $('.treeview > a').off('click').on('click', function(e) {
        e.preventDefault();
        var $this = $(this);
        var $parent = $this.parent();
        var $treeviewMenu = $this.next('.treeview-menu');
        
        if ($parent.hasClass('active')) {
            $parent.removeClass('active');
            $treeviewMenu.slideUp(200);
        } else {
            $('.treeview').removeClass('active');
            $('.treeview-menu').slideUp(200);
            $parent.addClass('active');
            $treeviewMenu.slideDown(200);
        }
    });
});
</script>

<?php include 'footer.php'; ?>
