/**
 * @package     RSGallery2
 *
 * resorting images in images view
 *
 * @subpackage  com_rsgallery2
 * @copyright   (C) 2016-2021 RSGallery2 Team
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @author      finnern
 * @since       4.3.0
 */

    // Change request from order element of gallery row:
    jQuery(document).ready(function ($) {
        var IsActive = false;

        jQuery(".changeOrder").change(
            function (event) {
                var Idx;
                var element;
                var Count;

                // Don't handle "Enter" otherwise for this control
                event.preventDefault();

                // alert ("Start reordering");

                // Exit for reentry check
                if (IsActive == true)
                {
                    alert ("Already started !!!");
                    return;
                }

                // activate re entrance check
                IsActive = true;

                var actElement = event.target;

                // Empty input
                if (actElement.value == '') {
                    alert ("Element Empty");
                    return;
                }

                var Ordering = ImagesOrdering;

                //--- User element order value --------------------------------------

                var strUserOrdering = actElement.value;
                var UserOrdering = parseInt(actElement.value);
                // var UserId = Ordering.GetGalleryId(actElement.id);
                var UserIdString = actElement.id; //

                UserIdString = UserIdString.replace( /^\D+/g, ''); // replace all leading non-digits with nothing
                var UserId = parseInt(UserIdString);

                var GalleryId = actElement.getAttribute('gallery_id');
                // alert ("Gallery ID: " + GalleryId);

                //--- Check limit user value --------------------------------------

                // Negative value will be corrected to lowest value
                if (UserOrdering < 0) {
                    UserOrdering = 0;
                    actElement.value = UserOrdering;
                }

                // Value higher than the count will be set to highest possible
                /* ==> may be set behind to ensure as last element
                if (UserOrdering > Count) {
                    UserOrdering = Count;
                    actElement.value = UserOrdering;
                }
                /**/

                //--- Fetch database ordering --------------------------------------

                var serverDbOrderingElement = jQuery("#dbOrdering");

                var serverDbOrderingValue = serverDbOrderingElement.val();
                if ((typeof(serverDbOrderingValue) === 'undefined') || (serverDbOrderingValue === null)) {
                    alert("serverDbOrdering is not defined ==> Server ordering values not exsisting");
                    return;
                }

                var oServerDbOrdering = jQuery.parseJSON (serverDbOrderingValue);

                //--- Fetch database galleries --------------------------------------

                var serverDbGalleriesElement = jQuery("#dbGalleries");

                var serverDbGalleriesValue = serverDbGalleriesElement.val();
                if ((typeof(serverDbGalleriesValue) === 'undefined') || (serverDbGalleriesValue === null)) {
                    alert("serverDbGalleriesValue is not defined ==> Server ordering values not exsisting");
                    return;
                }

                var oServerDbGalleries = jQuery.parseJSON (serverDbGalleriesValue);

                //-----------------------------------------
                // Order by parent / child
                //-----------------------------------------

                // Empty debug array
                Ordering.clearDebugTextArea ();

                //
                Ordering.initialize (oServerDbOrdering, GalleryId, oServerDbGalleries);
                //Ordering.displayDbOrderingArray ("(01) initialize");

                // Assign changed ordering by user to db elements
                Ordering.InsertUserOrdering (UserId, UserOrdering);
                //Ordering.displayDbOrderingArray ("(03) User ordering added");

                // Sort db array by ordering 
                Ordering.SortByOrdering ();
                //Ordering.displayDbOrderingArray ("(05) SortByOrdering");

                // Reassign without any holes '1,2,3,4,5 ...'
                Ordering.ResetOrdering (1); // actIdx=1
                //Ordering.displayDbOrderingArray ("(06) ResetOrdering");

                // Sort array by (new) ordering
                // Ordering.SortByOrdering ();
                //Ordering.displayDbOrderingArray ("(05) SortByOrdering");

                // Values for Get input in PHP
                serverDbOrderingElement.val(JSON.stringify(Ordering.dbOrdering));
                //Ordering.displayDbOrderingArray ("Saved back to 'INSERT'");

                // Save Ordering in HTML elements
                Ordering.AssignNewOrdering ();

                // alert ("end reordering");

                // Deactivate re entrance check
                IsActive = false;
			
            }
        );

        // For debug purposes: If activated it tells if jscript is working
        //alert ("assign successful");
    });
  
