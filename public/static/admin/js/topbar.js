/** topbar.js By Beginner Emain:zheng_jinfan@126.com HomePage:http://www.zhengjinfan.cn */
layui.define(['element', 'common'], function(exports) {
  "use strict";
  var $ = layui.jquery,
    layer = parent.layer === undefined ? layui.layer : parent.layer,
    element = layui.element,
    common = layui.common,
    cacheName = 'tb_topbar';

  var topbar = function() {
    /**
     *  默认配置
     */
    this.config = {
      elem: undefined, //容器
      data: undefined, //数据源
      url: undefined, //数据源地址
      type: 'GET', //读取方式
      cached: false, //是否使用缓存
      spreadOne: false //设置是否只展开一个二级菜单
    };
    this.v = '1.0.0';
  };
  //渲染
  topbar.prototype.render = function() {
    var _that = this;
    var _config = _that.config;
    if (typeof(_config.elem) !== 'string' && typeof(_config.elem) !== 'object') {
      common.throwError('topbar error: elem参数未定义或设置出错，具体设置格式请参考文档API.');
    }
    var $container;
    if (typeof(_config.elem) === 'string') {
      $container = $('' + _config.elem + '');
    }
    if (typeof(_config.elem) === 'object') {
      $container = _config.elem;
    }
    if ($container.length === 0) {
      common.throwError('topbar error:找不到elem参数配置的容器，请检查.');
    }
    if (_config.data === undefined && _config.url === undefined) {
      common.throwError('topbar error:请为topbar配置数据源.')
    }
    if (_config.data !== undefined && typeof(_config.data) === 'object') {
      var html = getHtml(_config.data);
      $container.html(html);
      element.init();
      _that.config.elem = $container;
    } else {
      if (_config.cached) {
        var cachetopbar = layui.data(cacheName);
        if (cachetopbar.topbar === undefined) {
          $.ajax({
            type: _config.type,
            url: _config.url,
            async: false, //_config.async,
            dataType: 'json',
            success: function(result, status, xhr) {
              //添加缓存
              layui.data(cacheName, {
                key: 'topbar',
                value: result
              });
              var html = getHtml(result);
              $container.html(html);
              element.init();
            },
            error: function(xhr, status, error) {
              common.msgError('topbar error:' + error);
            },
            complete: function(xhr, status) {
              _that.config.elem = $container;
            }
          });
        } else {
          var html = getHtml(cachetopbar.topbar);
          $container.html(html);
          element.init();
          _that.config.elem = $container;
        }
      } else {
        //清空缓存
        layui.data(cacheName, null);
        $.ajax({
          type: _config.type,
          url: _config.url,
          async: false, //_config.async,
          dataType: 'json',
          success: function(result, status, xhr) {
            var html = getHtml(result);
            $container.html(html);
            element.init();
          },
          error: function(xhr, status, error) {
            common.msgError('topbar error:' + error);
          },
          complete: function(xhr, status) {
            _that.config.elem = $container;
          }
        });
      }
    }

    //只展开一个二级菜单
    if (_config.spreadOne) {
      var $ul = $container.children('ul');
      $ul.find('li.layui-nav-item').each(function() {
        $(this).on('click', function() {
          $(this).siblings().removeClass('layui-nav-itemed');
        });
      });
    }
    return _that;
  };
  /**
   * 配置topbar
   * @param {Object} options
   */
  topbar.prototype.set = function(options) {
    var that = this;
    that.config.data = undefined;
    $.extend(true, that.config, options);
    return that;
  };
  /**
   * 绑定事件
   * @param {String} events
   * @param {Function} callback
   */
  topbar.prototype.on = function(events, callback) {
    var that = this;
    var _con = that.config.elem;
    if (typeof(events) !== 'string') {
      common.throwError('topbar error:事件名配置出错，请参考API文档.');
    }
    var lIndex = events.indexOf('(');
    var eventName = events.substr(0, lIndex);
    var filter = events.substring(lIndex + 1, events.indexOf(')'));
    if (eventName === 'click') {
      if (_con.attr('lay-filter') !== undefined) {
        _con.find('li').each(function() {
          var $this = $(this);
          $(this).on('click', function() {
            var $a = $(this).children('a');
            var id = $a.data('id');
            var title = $a.children('cite').text();
            $.getJSON("/admin/index/getleftnav/topid/" + id, function(result) {
              var data = {
                elem: $a,
                field: {
                  id: id,
                  title: title,
                  datajson:result
                }
              };
              callback(data);
            });
          });
        });
      }
      $('.admin-side-toggle1').on('click', function() {
        var sideWidth = $('#admin-side').width();
        $('.layui-layout-admin').toggleClass('on');
        $('.layui-nav-item').removeClass('layui-nav-itemed');
        if ($('.layui-layout-admin').hasClass('on')) {
          $(this).find('cite').html('展开菜单');
        } else {
          $(this).find('cite').html('关闭菜单');
        }


      });
    }
  };
  /**
   * 清除缓存
   */
  topbar.prototype.cleanCached = function() {
    layui.data(cacheName, null);
  };
  /**
   * 获取html字符串
   * @param {Object} data
   */
  function getHtml(data) {
    //debugger;
    var ulHtml = '';
    for (var i = 0; i < data.length; i++) {
      if (i==0) {
        ulHtml += '<li class="layui-nav-item layui-nav-itemed layui-this">';
      } else {
        ulHtml += '<li class="layui-nav-item">';
      }
      ulHtml += '<a href="javascript:;" data-id="' + data[i].id + '" >';
      ulHtml += '<cite>' + data[i].title + '</cite>'
      ulHtml += '</a>';
      ulHtml += '</li>';
    }
    ulHtml += '';

    return ulHtml;
  }

  var topbar = new topbar();

  exports('topbar', function(options) {
    return topbar.set(options);
  });
});
