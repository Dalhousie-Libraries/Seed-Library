/**
 * Function that removes extra information from the thumbnails link and
 * returns the link for the original image.
 */
function removeAdditionalInfo(link)
{
    // Returns null if link is empty
    if (!link)
        return null;

    // Start searching only after .dal.ca...
    var n1 = link.indexOf('-', 40);
    var n2 = link.indexOf('.', 40);

    if (n1 > 0)
        return link.substring(0, n1) + link.substring(n2);

    return link
}

// Logic for creating the modal box
$(document).ready(function() {
    // Image modal box (as seen in an example at http://fearlessflyer.com)
    $('li img').on('click', function() {
        var src = $(this).attr('src');
        var img = '<img src="' + src + '" class="img-responsive"/>';

        //start of new code new code
        var index = $(this).parent('li').index();

        var html = '';
        html += img;
        html += '<div style="height:25px;clear:both;display:block;">';
        html += '<a class="controls next" href="' + (index + 2) + '">next &raquo;</a>';
        html += '<a class="controls previous" href="' + (index) + '">&laquo; prev</a>';
        html += '</div>';

        $('#imagesModal').modal();
        $('#imagesModal').on('shown.bs.modal', function() {
            $('#imagesModal .modal-body').html(html);
            //new code
            $('a.controls').trigger('click');
        })
        $('#imagesModal').on('hidden.bs.modal', function() {
            $('#imagesModal .modal-body').html('');
        });
    });
});

// Logic for 'next' and 'prev' buttons
$(document).on('click', 'a.controls', function() {
    var index = $(this).attr('href');
    var src = $('ul.row li:nth-child(' + index + ') img').attr('src');

    $('.modal-body img').attr('src', removeAdditionalInfo(src));

    var newPrevIndex = parseInt(index) - 1;
    var newNextIndex = parseInt(newPrevIndex) + 2;

    if ($(this).hasClass('previous')) {
        $(this).attr('href', newPrevIndex);
        $('a.next').attr('href', newNextIndex);
    } else {
        $(this).attr('href', newNextIndex);
        $('a.previous').attr('href', newPrevIndex);
    }

    var total = $('ul.row li').length + 1;
    //hide next button
    if (total === newNextIndex) {
        $('a.next').hide();
    } else {
        $('a.next').show()
    }
    //hide previous button
    if (newPrevIndex === 0) {
        $('a.previous').hide();
    } else {
        $('a.previous').show()
    }

    return false;
});

// Handle keyboard control
$(document).keydown(function(e) {
    switch (e.which) {
        case 37: // left
            // Only allows click if available
            if ($('a.controls.previous').css('display') != 'none')
                $('a.controls.previous').click();
            break;

        case 39: // right
            if ($('a.controls.next').css('display') != 'none')
                $('a.controls.next').click();
            break;

        default:
            return; // exit this handler for other keys
    }
    e.preventDefault(); // prevent the default action (scroll / move caret)
});

// Logic for 'delete' buttons
$(document).on('click', '.delete', function(event) {
    event.preventDefault();
    return false;
});