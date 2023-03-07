/*
 * Edwiser_Forms - https://edwiser.org/
 * Version: 0.1.0
 * Author: Yogesh Shirsath
 */
define(['jquery'], function ($) {
    return {
        init: function() {
            var range = $(".input-range"),
              value = $(".range-value");
              console.log(range.attr("value"));
              value.html(range.attr("value"));

              range.on("input", function(){
                value.html(this.value);
                
                // Write value to hidden field
                $("input.test").val(this.value);
              });
        }
    };
});
