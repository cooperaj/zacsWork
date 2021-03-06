/*
 * jQuery UI 1.0 - New Wave User Interface
 *
 * Copyright (c) 2007 John Resig (jquery.com)
 * Dual licensed under the MIT (MIT-LICENSE.txt)
 * and GPL (GPL-LICENSE.txt) licenses.
 */
(function($){$.ui=$.ui||{};$.fn.tabs=function(initial,options){if(initial&&initial.constructor==Object){options=initial;initial=null;}
options=options||{};initial=initial&&initial.constructor==Number&&--initial||0;return this.each(function(){new $.ui.tabs(this,$.extend(options,{initial:initial}));});};$.each(['Add','Remove','Enable','Disable','Click','Load'],function(i,method){$.fn['tabs'+method]=function(){var args=arguments;return this.each(function(){var instance=$.ui.tabs.getInstance(this);instance[method.toLowerCase()].apply(instance,args);});};});$.fn.tabsSelected=function(){var selected=-1;if(this[0]){var instance=$.ui.tabs.getInstance(this[0]),$lis=$('li',this);selected=$lis.index($lis.filter('.'+instance.options.selectedClass)[0]);}
return selected>=0?++selected:-1;};$.ui.tabs=function(el,options){this.source=el;this.options=$.extend({initial:0,event:'click',disabled:[],unselected:false,unselect:options.unselected?true:false,spinner:'Loading&#8230;',cache:false,idPrefix:'tab-',fxSpeed:'normal',add:function(){},remove:function(){},enable:function(){},disable:function(){},click:function(){},hide:function(){},show:function(){},load:function(){},navClass:'ui-tabs-nav',selectedClass:'ui-tabs-selected',disabledClass:'ui-tabs-disabled',containerClass:'ui-tabs-container',hideClass:'ui-tabs-hide',loadingClass:'ui-tabs-loading'},options);this.tabify(true);var uuid='tabs'+$.ui.tabs.prototype.count++;$.ui.tabs.instances[uuid]=this;$.data(el,'tabsUUID',uuid);};$.ui.tabs.instances={};$.ui.tabs.getInstance=function(el){return $.ui.tabs.instances[$.data(el,'tabsUUID')];};$.extend($.ui.tabs.prototype,{count:0,tabify:function(init){this.$tabs=$('a:first-child',this.source);this.$containers=$([]);var self=this,o=this.options;this.$tabs.each(function(i,a){if(a.hash&&a.hash.replace('#','')){self.$containers=self.$containers.add(a.hash);}
else{$.data(a,'href',a.href);var id=a.title&&a.title.replace(/\s/g,'_')||o.idPrefix+(self.count+1)+'-'+(i+1);a.href='#'+id;self.$containers=self.$containers.add($('#'+id)[0]||$('<div id="'+id+'" class="'+o.containerClass+'"></div>').insertAfter(self.$containers[i-1]||self.source));}});if(init){this.$tabs.each(function(i,a){if(location.hash){if(a.hash==location.hash){o.initial=i;if($.browser.msie||$.browser.opera){var $toShow=$(location.hash),toShowId=$toShow.attr('id');$toShow.attr('id','');setTimeout(function(){$toShow.attr('id',toShowId);},500);}
scrollTo(0,0);return false;}}else if($(a).parents('li:eq(0)').is('li.'+o.selectedClass)){o.initial=i;return false;}});$(this.source).is('.'+o.navClass)||$(this.source).addClass(o.navClass);this.$containers.each(function(){var $this=$(this);$this.is('.'+o.containerClass)||$this.addClass(o.containerClass);});var $lis=$('li',this.source);this.$containers.addClass(o.hideClass);$lis.removeClass(o.selectedClass);if(!o.unselected){this.$containers.slice(o.initial,o.initial+1).show();$lis.slice(o.initial,o.initial+1).addClass(o.selectedClass);}
if($.data(this.$tabs[o.initial],'href')){this.load(o.initial+1,$.data(this.$tabs[o.initial],'href'));if(o.cache){$.removeData(this.$tabs[o.initial],'href');}}
for(var i=0,position;position=o.disabled[i];i++){this.disable(position);}}
var showAnim={},showSpeed=o.fxShowSpeed||o.fxSpeed,hideAnim={},hideSpeed=o.fxHideSpeed||o.fxSpeed;if(o.fxSlide||o.fxFade){if(o.fxSlide){showAnim['height']='show';hideAnim['height']='hide';}
if(o.fxFade){showAnim['opacity']='show';hideAnim['opacity']='hide';}}else{if(o.fxShow){showAnim=o.fxShow;}else{showAnim['min-width']=0;showSpeed=1;}
if(o.fxHide){hideAnim=o.fxHide;}else{hideAnim['min-width']=0;hideSpeed=1;}}
var resetCSS={display:'',overflow:'',height:''};if(!$.browser.msie){resetCSS['opacity']='';}
function hideTab(clicked,$hide,$show){$hide.animate(hideAnim,hideSpeed,function(){$hide.addClass(o.hideClass).css(resetCSS);if($.browser.msie){$hide[0].style.filter='';}
o.hide(clicked,$hide[0],$show&&$show[0]||null);if($show){showTab(clicked,$show,$hide);}});}
function showTab(clicked,$show,$hide){if(!(o.fxSlide||o.fxFade||o.fxShow)){$show.css('display','block');}
$show.animate(showAnim,showSpeed,function(){$show.removeClass(o.hideClass).css(resetCSS);if($.browser.msie){$show[0].style.filter='';}
o.show(clicked,$show[0],$hide&&$hide[0]||null);});}
function switchTab(clicked,$hide,$show){$(clicked).parents('li:eq(0)').addClass(o.selectedClass).siblings().removeClass(o.selectedClass);hideTab(clicked,$hide,$show);}
function tabClick(e){var $li=$(this).parents('li:eq(0)'),$hide=self.$containers.filter(':visible'),$show=$(this.hash);if(($li.is('.'+o.selectedClass)&&!o.unselect)||$li.is('.'+o.disabledClass)||o.click(this,$show[0],$hide[0])===false){this.blur();return false;}
if(o.unselect){if($li.is('.'+o.selectedClass)){$li.removeClass(o.selectedClass);self.$containers.stop();hideTab(this,$hide);this.blur();return false;}else if(!$hide.length){$li.addClass(o.selectedClass);self.$containers.stop();showTab(this,$show);this.blur();return false;}}
self.$containers.stop();if($show.length){if($.data(this,'href')){var a=this;self.load(self.$tabs.index(this)+1,$.data(this,'href'),function(){switchTab(a,$hide,$show);});if(o.cache){$.removeData(this,'href');}}else{switchTab(this,$hide,$show);}}else{throw'jQuery UI Tabs: Mismatching fragment identifier.';}
this.blur();return false;}
this.$tabs.unbind(o.event,tabClick).bind(o.event,tabClick);},add:function(url,text,position){if(url&&text){var o=this.options;position=position||this.$tabs.length;if(position>=this.$tabs.length){var method='insertAfter';position=this.$tabs.length;}else{var method='insertBefore';}
if(url.indexOf('#')==0){var $container=$(url);($container.length&&$container||$('<div id="'+url.replace('#','')+'" class="'+o.containerClass+' '+o.hideClass+'"></div>'))
[method](this.$containers[position-1]);}
$('<li><a href="'+url+'"><span>'+text+'</span></a></li>')
[method](this.$tabs.slice(position-1,position).parents('li:eq(0)'));this.tabify();o.add(this.$tabs[position-1],this.$containers[position-1]);}else{throw'jQuery UI Tabs: Not enough arguments to add tab.';}},remove:function(position){if(position&&position.constructor==Number){var $removedTab=this.$tabs.slice(position-1,position).parents('li:eq(0)').remove();var $removedContainer=this.$containers.slice(position-1,position).remove();this.tabify();this.options.remove($removedTab[0],$removedContainer[0]);}},enable:function(position){var $li=this.$tabs.slice(position-1,position).parents('li:eq(0)'),o=this.options;$li.removeClass(o.disabledClass);if($.browser.safari){$li.animate({opacity:1},1,function(){$li.css({opacity:''});});}
o.enable(this.$tabs[position-1],this.$containers[position-1]);},disable:function(position){var $li=this.$tabs.slice(position-1,position).parents('li:eq(0)'),o=this.options;if($.browser.safari){$li.animate({opacity:0},1,function(){$li.css({opacity:''});});}
$li.addClass(this.options.disabledClass);o.disable(this.$tabs[position-1],this.$containers[position-1]);},click:function(position){this.$tabs.slice(position-1,position).trigger('click');},load:function(position,url,callback){var self=this,o=this.options,$a=this.$tabs.slice(position-1,position).addClass(o.loadingClass),$span=$('span',$a),text=$span.html();if(url&&url.constructor==Function){callback=url;}
if(url){$.data($a[0],'href',url);}
if(o.spinner){$span.html('<em>'+o.spinner+'</em>');}
setTimeout(function(){$($a[0].hash).load(url,function(){if(o.spinner){$span.html(text);}
$a.removeClass(o.loadingClass);if(callback&&callback.constructor==Function){callback();}
o.load(self.$tabs[position-1],self.$containers[position-1]);});},0);}});})(jQuery);