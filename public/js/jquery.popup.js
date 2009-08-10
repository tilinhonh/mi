/* $Id: jquery.popup.js 67 2008-01-22 08:54:37Z Bolik@xjgc.com $ */

(function($) {
  function Popup(el, content) {
    this.input = $(el);
    this.popupContent = content;
    
    this.input.attr("readonly", "readonly");
    
    this.bindMethodsToObj("show", "hide", "hideIfClickOutside", "selectItem");
    
    this.build();
    //this.selectItem();
    this.hide();
  };

  Popup.prototype = {
    build: function() {
      this.ClearController = $('<a href="#">clear</a>').click(this.bindToObj(function(event) {
        this.selectItem("", "");
        this.hide();
        return false;
      })); 
        
      this.popupController = $('<div class="popupController"></div>').append(this.ClearController); 
      this.popupContent = $('<div class="popupContent"></div>').append(this.popupContent); 
     
      this.popupPanel = this.rootLayers = $('<div class="popupPanel"></div>')
        .css({ display: "none", position: "absolute", zIndex: 100 })
        .append(this.popupController, this.popupContent)
        .appendTo(document.body);
      
      if ($.browser.msie && $.browser.version < 7) {
        this.ieframe = $('<iframe class="popupPanel_ieframe" frameborder="0" src="#"></iframe>')
          .css({ position: "absolute", display: "none", zIndex: 99 })
          .insertBefore(this.popupPanel);
        this.rootLayers = this.rootLayers.add(this.ieframe);
      };
      
      $("a", this.popupContent).click(this.bindToObj(function(event) {
        this.selectItem($(event.target).attr("id"), $(event.target).attr("innerHTML"));
        this.hide();
        return false;
      }));

      //this.input.change(this.bindToObj(function() { this.selectItem(); }));
    }, 
    
    selectItem: function(item, text) {  
      this.selectedItem = item;
      this.input.attr("SelectedItem", item);
      this.input.val(text);      
    },
    
    show: function() {
      this.setPosition();
      this.rootLayers.css("display", "block");
      this.input.unbind("focus", this.show);
      $(document.body).click(this.hideIfClickOutside);
    },
    
    hide: function() {
      this.rootLayers.css("display", "none");
      $(document.body).unbind("click", this.hideIfClickOutside);
      this.input.focus(this.show);
    },
    
    hideIfClickOutside: function(event) {
      if (event.target != this.input[0] && !this.insideSelector(event)) {
        this.hide();
      };
    }, 
     
    setPosition: function() {
      var offset = this.input.offset();
      this.rootLayers.css({
        top: offset.top + this.input.outerHeight(),
        left: offset.left
      });
      
      if (this.ieframe) {
        this.ieframe.css({
          width: this.popupPanel.outerWidth(),
          height: this.popupPanel.outerHeight()
        });
      };
    },  
     
    insideSelector: function(event) {
      var offset = this.popupPanel.offset();
      offset.right = offset.left + this.popupPanel.outerWidth();
      offset.bottom = offset.top + this.popupPanel.outerHeight();
      
      return event.pageY < offset.bottom &&
             event.pageY > offset.top &&
             event.pageX < offset.right &&
             event.pageX > offset.left;
    },
    
    bindToObj: function(fn) {
      var self = this;
      return function() { return fn.apply(self, arguments) };
    },
    
    bindMethodsToObj: function() {
      for (var i = 0; i < arguments.length; i++) {
        this[arguments[i]] = this.bindToObj(this[arguments[i]]);
      };
    } 
    
  };

  $.fn.popup = function(content) {
    return this.each(function() { 
      new Popup(this, content); 
    });
  };
})(jQuery);