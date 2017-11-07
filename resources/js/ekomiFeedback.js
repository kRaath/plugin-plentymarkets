if ( (typeof jQuery === 'undefined') && !window.jQuery ) {
  // document.write(unescape("%3Cscript type='text/javascript' src='http://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js'%3E%3C/script%3E"));
} else {
 if((typeof jQuery === 'undefined') && window.jQuery) {
     jQuery = window.jQuery;
 } else if((typeof jQuery !== 'undefined') && !window.jQuery) {
     window.jQuery = jQuery;
 }
}

if(typeof jQuery !== 'undefined'){
    jQuery(document).ready(function () {
    // sorting reviews data
    jQuery('.ekomi_reviews_sort').on('change', function (e) {
        filter = this.value;
        pageOffset = 0;
        var data = {
            type: 'loadReviews',
            articleId: articleId,
            queryBy: queryBy,
            pageOffset: pageOffset,
            reviewsLimit: reviewsLimit,
            filter: filter
        };

        jQuery.ajax({
            type: "POST",
            url: ajaxUrl,
            data: data,
            cache: false,
            success: function (data) {
                var json = jQuery.parseJSON(data);

                jQuery('#ekomi_reviews_container').html(json.reviews_data.result);

                // reset the page offset

                pageReviewsCount = json.reviews_data.count;

                jQuery('.current_review_batch').text(pageReviewsCount);
                jQuery('.loads_more_reviews').show();
            }
        });
    });

    // saving users feedback on reviews
    jQuery('body').on('click', '.ekomi_review_helpful_button', function () {
        var current = jQuery(this);

        var data = {
            type: 'saveFeedback',
            articleId: articleId,
            review_id: jQuery(this).data('review-id'),
            helpfulness: jQuery(this).data('review-helpfulness')
        };

        jQuery.ajax({
            type: "POST",
            url: ajaxUrl,
            data: data,
            cache: false,
            success: function (data) {
                var json = jQuery.parseJSON(data);

                current.parent('.ekomi_review_helpful_question').hide();
                current.parent().prev('.ekomi_review_helpful_thankyou').show();
                var infoMsg= json.helpfull_count+" "+jQuery('.ekomi_prc_out_of').text()+" "+json.total_count+" "+jQuery('.ekomi_prc_people_found').text();
                current.parent().prev().prev('.ekomi_review_helpful_info').html(infoMsg);
            }
        });
    });

    // Loading reviews on paginatin
    jQuery('body').on('click', '.loads_more_reviews', function (e) {
        pageOffset = pageReviewsCount;

        if (reviewsCount / pageReviewsCount > 1) {
            var data = {
                type: 'loadReviews',
                articleId: articleId,
                queryBy: queryBy,
                pageOffset: pageOffset,
                reviewsLimit: reviewsLimit,
                filter: filter
            };

            jQuery.ajax({
                type: "POST",
                url: ajaxUrl,
                data: data,
                cache: false,
                success: function (data) {
                    var json = jQuery.parseJSON(data);

                    pageReviewsCount = pageReviewsCount + json.reviews_data.count;
                    jQuery('#ekomi_reviews_container').append(json.reviews_data.result);
                    jQuery('.current_review_batch').text(pageReviewsCount);

                    if (reviewsCount / pageReviewsCount <= 1) {
                        jQuery('.loads_more_reviews').hide();
                    }
                }
            });
        } else {
            jQuery('.loads_more_reviews').hide();
        }
    });
});
}