define(['uiComponent', 'ko',
         'jquery',
         'mage/url',
         'domReady!'], 
        function (component, ko, $, url) {
    'use strict';

    const _initializeSectionData = {
        count: 0,
        items: undefined
    }

    url.setBaseUrl(window.BASE_URL);
    var contentUrl = url.build('random3/content/sidebar');

// Public:
return component.extend({

    initialize: function() {
        this._super();
       
        this.customSidebar = ko.observable( _initializeSectionData);

        var self = this;
        $.ajax({
            url: contentUrl,
            method: 'POST',
            data: {
                "request_path": window.location.pathname,
                "category_id": $("#talexan-random-three-product").attr("data-category-id")
            },
            success: function (data){
                try{
                    var content = (typeof data === "string")?
                                                JSON.parse(data): data;

                    if (content.count==undefined)
                        throw "Random3Error: Unknown object in response";

                    self.customSidebar(content);
                    $('.block-custom-sidebar').trigger('contentUpdate');
                   
 //                   console.log("customSidebar.count = ", self.customSidebar().count);
 //                   console.log("customSidebar.items = ", self.customSidebar().items);
                }
                catch(e){
                    console.error(e);
                }
            },
            error: function ( jqXHR, textStatus){
                console.error("Error status: ", textStatus);
                console.log("Object jQuery: ", jqXHR);
            }
        });
        }
    });
});