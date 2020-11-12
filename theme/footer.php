
  </div>
</div>
	<!-- Placed at the end of the document so the pages load faster -->
	<script src="js/popper.min.js"></script>
	<script src="js/jquery-3.4.1.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script type="application/javascript">
	
	<?php if($showErrorDesc) { ?>
			$('.errorDesc').toggle(); 
			$('#toggleError').html('[-]');
	<?php } ?>
		$(function () {
		$('[data-toggle="tooltip"]').tooltip()
		})
	</script>
	<script type="text/javascript">
	$('#toggleError').click(function(ev) { 
		$('.errorDesc').toggle(); 
		$(this).html(($('#toggleError').text() == '[+]') ? '[-]' : '[+]');
	})
	</script>

<div class="font-weight-bold text-center py-3"><small>KasparK 2020</small></div>
</body>

</html>
