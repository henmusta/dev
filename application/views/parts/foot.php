		</main>
		<?php include_once('footer.php');?>
	</div>
	<script src="assets/js/oneui.core.min.js"></script>
	<script src="assets/js/oneui.app.min.js"></script>
	<?php 
	if(isset($javascripts)): 
		foreach($javascripts AS $src) : echo '<script src="' . $src . '"></script>' ; endforeach;
		unset($javascripts,$src);
	endif;
	if( isset($scripts) && is_array($scripts) && count($scripts) > 0 ): 
		foreach($scripts AS $key => $script) : echo $script; endforeach;
		unset($scripts,$key,$script);
	endif;
	?>
</body>
</html>