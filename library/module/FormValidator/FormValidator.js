(function($, window) {
  var Captcha, Messaging, Validator, forceClass, human, interpreter;
  interpreter = {
    required: 'この項目は必須です。',
    invalid: '入力された値が不正です。',
    maxlen: '入力が長ぎます。${1} 文字以下にして下さい。',
    minlen: '入力が短すぎます。${1} 文字以上ご入力下さい。',
    maximum: '入力された値が大き過ぎます。${1} 以下をご入力下さい。',
    minimum: '入力された値が小さ過ぎます。${1} 以上をご入力下さい。',
    maxselect: '選択された項目が多すぎます。${1} 個以内で選択して下さい。',
    minselect: '選択された項目が少なすぎます。${1} 個以上を選択して下さい。',
    select: '選択された項目の数が不正です。${1} 個を選択して下さい。',
    success: '送信しました。ありがとうございました。',
    fail: 'ご入力された内容の ${1} 箇所に不備があります。',
    rejected: '送信できませんでした。しばらく待ってからもう一度お試し下さい。'
  };
  human = function(text) {
    var _ref;
    if ($.isArray(text)) {
      if (interpreter[text[0]]) {
        return interpreter[text[0]].replace(/\$\{(\d+)\}/g, function(_0, _1) {
          return text[+_1];
        });
      } else {
        return text[0];
      }
    } else {
      return (_ref = interpreter[text]) != null ? _ref : text;
    }
  };
  forceClass = function(element, newClass, oldClass) {
    return element.removeClass(oldClass).addClass(newClass);
  };
  Messaging = (function() {

    function Messaging(Validator) {
      this.Validator = Validator;
      this.cache = {};
      this.cls = this.Validator.config.className;
      this.html = this.Validator.config.html;
    }

    Messaging.prototype.getElement = function(name, html) {
      var box, group, target;
      if (this.cache[name]) {
        return this.cache[name];
      }
      target = $('#' + this.Validator.getName(name));
      if (this.Validator.config.groupSelector) {
        group = target.closest(this.Validator.config.groupSelector);
      } else {
        group = target.parent();
      }
      box = $(html);
      group.append(box);
      return this.cache[name] = {
        target: target,
        box: box,
        group: group
      };
    };

    Messaging.prototype._message = function(name, msg) {
      var el;
      el = this.getElement(name, this.html.message);
      msg = human(msg);
      el.box.removeClass('hide');
      if ('valid' === msg) {
        forceClass(el.box, this.cls.vaild, this.cls.invalid);
        return forceClass(el.group, 'success', 'error');
      } else {
        el.box.text(msg);
        forceClass(el.box, this.cls.invalid, this.cls.vaild);
        return forceClass(el.group, 'error', 'success');
      }
    };

    Messaging.prototype._hide = function(name) {
      var el;
      el = this.getElement(name);
      forceClass(el.box, this.cls.valid, this.cls.invalid);
      el.group.removeClass('error success');
      return el.box.addClass('hide');
    };

    Messaging.prototype.hideAll = function() {
      var name, _results;
      _results = [];
      for (name in this.cache) {
        _results.push(this._hide(name));
      }
      return _results;
    };

    Messaging.prototype.message = function(message) {
      var msg, name, _results;
      _results = [];
      for (name in message) {
        msg = message[name];
        _results.push(this._message(name, msg));
      }
      return _results;
    };

    Messaging.prototype.notification = function(success, count) {
      var el;
      el = this.getElement('submitButton', this.html.notification);
      el.box.removeClass('hide');
      if (success) {
        el.box.text(human(['success', count]));
        return forceClass(el.box, this.cls.success, this.cls.fail);
      } else if (count > 0) {
        el.box.text(human(['fail', count]));
        return forceClass(el.box, this.cls.fail, this.cls.success);
      } else {
        el.box.text(human('rejected'));
        return forceClass(el.box, this.cls.fail, this.cls.success);
      }
    };

    return Messaging;

  })();
  Captcha = (function() {

    function Captcha(image, input) {
      var _this = this;
      this.image = image;
      this.input = input;
      this.path = this.image.attr('src');
      this.image.on('click', function() {
        return _this.refresh();
      });
    }

    Captcha.prototype.refresh = function() {
      var rand;
      rand = Math.floor(Math.random() * 100000);
      this.image.attr('src', this.path + "?t=" + rand);
      return this.input.val('').focus();
    };

    return Captcha;

  })();
  Validator = (function() {

    function Validator(form, config) {
      var id;
      this.form = form;
      this.config = config;
      this.config = $.extend({
        prefix: null,
        groupSelector: '.control-group',
        html: {
          message: '<p class="validation-message"></p>',
          notification: '<p class="validation-notification"></p>'
        },
        className: {
          vaild: 'hide',
          invalid: 'show',
          success: 'success',
          fail: 'fail'
        },
        init: $.noop,
        beforeSubmit: function() {
          return true;
        },
        success: $.noop,
        fail: $.noop
      }, $.FormValidator.defaults, this.config);
      if (!this.config.prefix) {
        id = /#([\w\-]+)/.exec(this.form.selector);
        this.config.prefix = (id != null ? id : ['', 'form'])[1];
      }
      this.Messaging = new Messaging(this);
      this.submitButton = this.form.find('button[type=submit]');
      this.submitButton.eq(0).attr('id', this.getName('submitButton'));
      if ($('#captcha-image').length === 1) {
        this.captchaImage = $('#captcha-image');
        this.captchaInput = $('#' + this.getName('captcha'));
        new Captcha(this.captchaImage, this.captchaInput);
      }
      this.config.init(this.form, this);
      this.init();
      this.config.afterSetup(this.form, this);
    }

    Validator.prototype.init = function() {
      var _this = this;
      return this.form.ajaxForm({
        dataType: 'json',
        data: {
          _ajax_call: 1
        },
        beforeSubmit: function(data, $form) {
          _this.disableButton();
          return _this.config.beforeSubmit(_this.form, _this);
        },
        success: function(data, status, xhr) {
          _this.enableButton();
          _this.Messaging.hideAll();
          _this.Messaging.notification(data.processed, data.error_count);
          _this.Messaging.message(data.error_message);
          if (data.processed) {
            _this.config.success(_this.form, _this);
            return true;
          } else {
            _this.config.fail(_this.form, _this);
            return false;
          }
        },
        error: function() {
          _this.enableButton();
          return _this.config.fail(_this.form, _this);
        }
      });
    };

    Validator.prototype.getName = function(name) {
      return this.config.prefix + '-' + name;
    };

    Validator.prototype.disableButton = function() {
      return this.submitButton.attr('disabled', true).addClass('loading');
    };

    Validator.prototype.enableButton = function() {
      return this.submitButton.attr('disabled', false).removeClass('loading');
    };

    return Validator;

  })();
  $.fn.FormValidator = function(config) {
    new Validator(this, config);
    return this;
  };
  return $.FormValidator = {
    text: interpreter,
    defaults: {}
  };
})(jQuery, window);
