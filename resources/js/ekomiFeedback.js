if ((typeof jQuery === 'undefined') && !window.jQuery) {
    // document.write(unescape("%3Cscript type='text/javascript' src='http://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js'%3E%3C/script%3E"));
} else {
    if ((typeof jQuery === 'undefined') && window.jQuery) {
        jQuery = window.jQuery;
    } else if ((typeof jQuery !== 'undefined') && !window.jQuery) {
        window.jQuery = jQuery;
    }
}

if (typeof jQuery !== 'undefined') {
    jQuery(document).ready(function () {
        // sorting reviews data
        jQuery('.ekomi_reviews_sort').on('change', function (e) {
            prcFilter = this.value;
            prcOffset = 0;
            var data = {
                prcItemID: prcItemID,
                prcOffset: prcOffset,
                reviewsLimit: reviewsLimit,
                prcFilter: prcFilter
            };

            jQuery.ajax({
                type: "POST",
                url: prcBaseUrl + 'loadReviews',
                data: data,
                cache: false,
                success: function (data) {
                    var json = jQuery.parseJSON(data);

                    jQuery('#ekomi_reviews_container').html(json.result);

                    // reset the page offset

                    reviewsCountPage = json.count;

                    jQuery('.current_review_batch').text(reviewsCountPage);
                    jQuery('.loads_more_reviews').show();
                }
            });
        });

        // saving users feedback on reviews
        jQuery('body').on('click', '.ekomi_review_helpful_button', function () {
            var current = jQuery(this);

            var data = {
                prcItemID: prcItemID,
                review_id: jQuery(this).data('review-id'),
                helpfulness: jQuery(this).data('review-helpfulness')
            };

            jQuery.ajax({
                type: "POST",
                url: prcBaseUrl + 'saveFeedback',
                data: data,
                cache: false,
                success: function (data) {
                    var json = jQuery.parseJSON(data);

                    current.parent('.ekomi_review_helpful_question').hide();
                    current.parent().prev('.ekomi_review_helpful_thankyou').show();
                    current.parent().prev().prev('.ekomi_review_helpful_info').html(json.message);
                }
            });
        });

        // Loading reviews on paginatin
        jQuery('body').on('click', '.loads_more_reviews', function (e) {
            prcOffset = reviewsCountPage;

            if (reviewsCountTotal / reviewsCountPage > 1) {
                var data = {
                    prcItemID: prcItemID,
                    prcOffset: prcOffset,
                    reviewsLimit: reviewsLimit,
                    prcFilter: prcFilter
                };

                jQuery.ajax({
                    type: "POST",
                    url: prcBaseUrl + 'loadReviews',
                    data: data,
                    cache: false,
                    success: function (data) {
                        var json = jQuery.parseJSON(data);

                        reviewsCountPage = reviewsCountPage + json.count;
                        jQuery('#ekomi_reviews_container').append(json.result);
                        jQuery('.current_review_batch').text(reviewsCountPage);

                        if (reviewsCountTotal / reviewsCountPage <= 1) {
                            jQuery('.loads_more_reviews').hide();
                        }
                    }
                });
            } else {
                jQuery('.loads_more_reviews').hide();
            }
        });

        jQuery('#ekomi_prc_reviews').on('click', function (e) {
            e.preventDefault();
            jQuery('html, body').animate({
                scrollTop: jQuery(".nav-tabs").offset().top
            }, 1800);

            jQuery(".nav-tabs .nav-item:last a").click();
        });

    });
}