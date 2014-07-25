<!-- Modal Dialog -->
<div class="modal fade" id="confirmDelete" role="dialog" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Delete Permanently</h4>
      </div>
      <div class="modal-body">
        <p>Are you sure about this ?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal" id="confirm">Delete</button>
      </div>
    </div>
  </div>
</div>
<!-- Dialog show event handler -->
<script type="text/javascript">
  $('#confirmDelete').on('show.bs.modal', function (e) {
      // Save related target
      $(this).find('.modal-footer #confirm').data('source', e.relatedTarget);
      
      // CUstomize message and title
      $message = $(e.relatedTarget).attr('data-message');
      $(this).find('.modal-body p').text($message);
      $title = $(e.relatedTarget).attr('data-title');
      $(this).find('.modal-title').text($title);

      // Pass form reference to modal for submission on yes/ok
      var link = $(e.relatedTarget).attr('data-link');
      $(this).find('.modal-footer #confirm').data('form', link);
  });

  // Should be implemented individually in each page that includes this template
  //$('#confirmDelete').find('.modal-footer #confirm').on('click', function(){
      //$(this).data('form').submit();
      //alert($(this).data('form'));
  //});
</script>