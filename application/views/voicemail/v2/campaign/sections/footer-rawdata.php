      <hr>

      <footer>
        <p>Copyright &copy; {$domain_name} &reg; 2017, All Rights Reserved</p>
      </footer>

    </div> <!-- /container -->
    
    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
<!--    <script src="/static/js/jquery.min.js"></script>-->
 
    <script src="{$base_url}static/js/bootstrap.js"></script>

<!--    <script src="{$base_url}static/js/additional-methods.min.js"></script>
    <script src="{$base_url}static/js/jquery-ui-1.8.20.custom.min.js"></script>
    <script src="{$base_url}static/js/jquery.timepicker.js"></script>
    <script src="{$base_url}static/js/campclick.js"></script>-->
 	<script src="http://code.jquery.com/jquery-1.9.1.js"></script>  
	<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>  
    <script src="{$base_url}static/js/jquery.validate.min.js"></script> 
    <script type="text/javascript">
	 var j = $.noConflict();
	j(function () {
	  j('#dt-range-selector').click(function(){
											//$('#dt-range-selector').addClass('btn-inverse');
											    j('.btn').removeClass('btn-inverse');
												j('#dt-range-selector').toggleClass('btn-inverse');
												j('#date-selection-form').toggle('slow');
											 });
	  
	  j('#date-select').submit(function(){
										var st_date = j('#startDate').val();
										var ed_date = j('#endDate').val();
										var action_url = j('#action_url').val();
										window.location = action_url+'/'+st_date+'/'+ed_date;
										return false;
										});
	});
	</script>
     <script>
	j(function() {
		j("#startDate").datepicker({ dateFormat: "yy-mm-dd" });
		j("#endDate").datepicker({ dateFormat: "yy-mm-dd" });
	});
	</script>

  </body>
</html>
