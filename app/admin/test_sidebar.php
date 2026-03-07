<?php include 'header.php'; ?>

<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Test Sidebar
      <small>Simple test page</small>
    </h1>
  </section>

  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title">Sidebar Test</h3>
          </div>
          
          <div class="box-body">
            <p>This is a simple test page. Try clicking on the sidebar menu items.</p>
            <p>Check the browser console (F12) for debug messages.</p>
            
            <div id="test-results"></div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<script>
$(document).ready(function() {
    
    // Test sidebar functionality
    $('.sidebar-menu a').on('click', function(e) {
        var linkText = $(this).text().trim();
        
        $('#test-results').append('<p>Clicked: ' + linkText + '</p>');
    });
    
    // Force sidebar to be clickable
    $('.sidebar-menu a').css({
        'pointer-events': 'auto',
        'cursor': 'pointer',
        'z-index': '9999'
    });
});
</script>

<?php include 'footer.php'; ?>
