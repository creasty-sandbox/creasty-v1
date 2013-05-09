
do ($ = jQuery, window) ->

	# Interpreter

	interpreter =
		required:  'この項目は必須です。'
		invalid:   '入力された値が不正です。'
		maxlen:    '入力が長ぎます。${1} 文字以下にして下さい。'
		minlen:    '入力が短すぎます。${1} 文字以上ご入力下さい。'
		maximum:   '入力された値が大き過ぎます。${1} 以下をご入力下さい。'
		minimum:   '入力された値が小さ過ぎます。${1} 以上をご入力下さい。'
		maxselect: '選択された項目が多すぎます。${1} 個以内で選択して下さい。'
		minselect: '選択された項目が少なすぎます。${1} 個以上を選択して下さい。'
		select:    '選択された項目の数が不正です。${1} 個を選択して下さい。'
		success:   '送信しました。ありがとうございました。'
		fail:      'ご入力された内容の ${1} 箇所に不備があります。'
		rejected:  '送信できませんでした。しばらく待ってからもう一度お試し下さい。'

	human = (text) ->
		if $.isArray text
			if interpreter[text[0]]
				interpreter[text[0]].replace /\$\{(\d+)\}/g, (_0, _1) -> text[+_1]
			else
				text[0]
		else
			interpreter[text] ? text

	# Utilities

	forceClass = (element, newClass, oldClass) ->
		element.removeClass(oldClass).addClass newClass

	# Messaging

	class Messaging
		constructor: (@Validator) ->
			@cache = {}
			@cls = @Validator.config.className
			@html = @Validator.config.html

		getElement: (name, html) ->
			return @cache[name] if @cache[name]

			target = $ '#' + @Validator.getName name

			if @Validator.config.groupSelector
				group = target.closest @Validator.config.groupSelector
			else
				group = target.parent()

			box = $ html
			group.append box

			@cache[name] = {target, box, group}

		_message: (name, msg) ->
			el = @getElement name, @html.message

			msg = human msg

			el.box.removeClass 'hide'

			if 'valid' == msg
				forceClass el.box, @cls.vaild, @cls.invalid
				forceClass el.group, 'success', 'error'
			else
				el.box.text msg
				forceClass el.box, @cls.invalid, @cls.vaild
				forceClass el.group, 'error', 'success'

		_hide: (name) ->
			el = @getElement name

			forceClass el.box, @cls.valid, @cls.invalid
			el.group.removeClass 'error success'

			el.box.addClass 'hide'

		hideAll: () ->
			@_hide name for name of @cache

		message: (message) ->
			@_message name, msg for name, msg of message

		notification: (success, count) ->
			el = @getElement 'submitButton', @html.notification

			el.box.removeClass 'hide'

			if success
				el.box.text human ['success', count]
				forceClass el.box, @cls.success, @cls.fail
			else if count > 0
				el.box.text human ['fail', count]
				forceClass el.box, @cls.fail, @cls.success
			else
				el.box.text human 'rejected'
				forceClass el.box, @cls.fail, @cls.success

	# Captcha

	class Captcha
		constructor: (@image, @input) ->
			@path = @image.attr 'src'

			@image.on 'click', () => @refresh()

		refresh: () ->
			rand = Math.floor Math.random() * 100000
			@image.attr 'src', @path + "?t=" +rand
			@input.val('').focus()

	# Validator

	class Validator
		constructor: (@form, @config) ->
			@config = $.extend
				prefix: null
				groupSelector: '.control-group'
				html:
					message:      '<p class="validation-message"></p>'
					notification: '<p class="validation-notification"></p>'
				className:
					vaild:   'hide'
					invalid: 'show'
					success: 'success'
					fail:    'fail'
				init: $.noop
				beforeSubmit: () -> true
				success: $.noop
				fail: $.noop
			, $.FormValidator.defaults, @config

			if !@config.prefix
				id = /#([\w\-]+)/.exec @form.selector
				@config.prefix = (id ? ['', 'form'])[1]

			@Messaging = new Messaging @

			@submitButton = @form.find 'button[type=submit]'
			@submitButton.eq(0).attr 'id', @getName 'submitButton'

			if $('#captcha-image').length == 1
				@captchaImage = $ '#captcha-image'
				@captchaInput = $ '#' + @getName 'captcha'

				new Captcha @captchaImage, @captchaInput

			@config.init @form, @
			@init()
			@config.afterSetup @form, @

		init: () ->
			@form.ajaxForm
				dataType: 'json'
				data: _ajax_call: 1

				beforeSubmit: (data, $form) =>
					@disableButton()
					@config.beforeSubmit @form, @

				success: (data, status, xhr) =>
					@enableButton()

					@Messaging.hideAll()
					@Messaging.notification data.processed, data.error_count
					@Messaging.message data.error_message

					if data.processed
						@config.success @form, @
						true
					else
						@config.fail @form, @
						false

				error: () =>
					@enableButton()

					@config.fail @form, @

		getName: (name) -> @config.prefix + '-' + name

		disableButton: () ->
			@submitButton.attr('disabled', true).addClass 'loading'

		enableButton: () ->
			@submitButton.attr('disabled', false).removeClass 'loading'

	# FormValidator

	$.fn.FormValidator = (config) ->
		new Validator @, config
		@

	$.FormValidator =
		text: interpreter
		defaults: {}

