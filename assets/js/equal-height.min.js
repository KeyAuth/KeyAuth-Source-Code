/**
* jQuery Simple Equal Heights per row
* 
* Copyright (c) 2016 iBird Rose & KFan
* Dual licensed under the MIT and GPL licenses.
*
* Version 1.0.0
*/
!function(t){"use strict";t(document).on("ready",function(){jQuery.fn.extend({equalHeights:function(e){var n=this;e=t.extend({innerItem:!1,parent:t(this).parent(),perRow:!0},e),this.setItemsWithInner=function(e,n,i,h){i?"auto"==n?t(e).find(i).height(n):t(e).find(i).each(function(){var e=n;t(this).siblings().each(function(){e-=t(this).outerHeight()});i=this;do{var i=t(i).parent();e-=parseInt(t(i).css("padding-top")),e-=parseInt(t(i).css("padding-bottom"))}while(!t(i).is(h));t(this).height(e)}):t(e).height(n)};var i=t(this);return t(e.parent).each(function(){var h=[],s=[],r=0,a=0,u=this;h=e.parent.length>1?t(this).find(i):i,n.setItemsWithInner(h,"auto",e.innerItem),t(h).each(function(){var i=t(this).outerWidth(!0),h=t(this).height();if(i+r>t(u).width()&&s.length>0&&e.perRow)return n.setItemsWithInner(s,a,e.innerItem,u),s=[this],a=h,void(r=i);a<h&&(a=h),s.push(this),r+=i}),s.length>0&&(n.setItemsWithInner(s,a,e.innerItem,u),s=[])}),this}}),t(document).ready(function(){t("[data-equalheights]").each(function(){var e=t(this).attr("data-equalheights"),n=t(this).find(e);t(n).equalHeights(),t(window).resize(function(){t(n).equalHeights()})})})})}(jQuery);
