define(['uiComponent', 
        'Magento_Customer/js/customer-data', 'jquery'], 
        function (component, customData, $) {
    'use strict';

    const _sectionName = 'custom-sidebar';
    const _initializeSectionData = {
        count: 0,
        items: undefined
    }

// Public:
return component.extend({

    initialize: function() {
        this._super();

        customData.set(_sectionName, _initializeSectionData);
       
        this.customSidebar = customData.get(_sectionName);

        var self = this;
        customData.reload([_sectionName], false).done(function(){
            self.customSidebar = customData.get(_sectionName);
            console.log("customSidebar.count = ", self.customSidebar().count);
            console.log("customSidebar.items = ", self.customSidebar().items);
        });

//        console.log("customSidebar.count = ", this.customSidebar().count);
//        console.log("customSidebar.items = ", this.customSidebar().items);
    }
});
});