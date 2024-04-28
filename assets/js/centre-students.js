(function($) {
  $('#select_centres').change(function() {
    const centreID = $(this).val()

    if (centreID == 'all') {
        $('.centre-students tr.blurb').css('display', 'revert')
        return false
    }
    
    $('.centre-students tr.blurb').css('display', 'none')
    $('.centre-students tr.blurb.centre-' + centreID).css('display', 'revert')
})
})(jQuery)