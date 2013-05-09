
do ($ = jQuery, document) ->
	class FormData
		construct: (form) ->
			@create form

		create: (form) ->
			return unless form
			@form = $ form

		get: () ->
			a = {}

			@form.find('input,textarea,select').each (i, el) =>
				n = el.name
				v = @field el

				return unless n

				if n.slice -2 == '[]'
					n = n.slice 0, -2

					unless $.isArray a[n]
						a[n] = []

				if v != null
					if /^[0-9]+$/.test v
						v = parseFloat v

					if $.isArray a[n]
						a[n].push v
					else
						a[n] = v

			a

		field: (el) ->
			n = el.name
			t = el.type
			tag = el.tagName.toLowerCase()

			return if !n  || el.disabled || t == 'reset' || t == 'button' || (t == 'checkbox' || t == 'radio') && !el.checked || (t == 'submit' || t == 'image') && el.form && el.form.clk != el || tag == 'select' && el.selectedIndex == -1

			if 'select' == tag
				index = el.selectedIndex;
				return if index < 0

				a = []
				ops = el.options
				one = !!('select-one' == t)
				max = if one then index + 1 else ops.length

				for i in [(if one then index else 0)...max] by 1
					op = ops[i]

					if op.selected
						v = op.value ? op.text
						return v if one
						a.push v

				return a

			$(el).val()

	util =
		selectAction: () ->
			# TODO

		InputTitleOverlay: (selector) ->
			$(selector ? '.autofill').each () ->
				$this = $ @
				d = $this.attr 'title'

				document.activeElement != $this[0] && $this.val d

				$this.focus () ->
					$this.val '' if d == $this.val()
				.blur () ->
					$this.val d if '' == $this.val()

	$.fn.FormData = () ->
		new FormData $ @
		@
