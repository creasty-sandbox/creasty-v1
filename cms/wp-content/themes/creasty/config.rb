require 'fileutils'
require 'shellwords'

http_path = "/"
css_dir = "/"
sass_dir = "/sass"
images_dir = "/images"
fonts_dir = "/images/fonts"
javascripts_dir = "/javascripts"

output_style =
	case environment
		when :prod then :compressed
		when :dev then :compact
		when :debug then :expanded
	end

relative_assets = true
line_comments = false


on_stylesheet_saved do |filename|
	filebase = Shellwords.escape File.basename(filename)

	`terminal-notifier -title 'Compass: #{filebase}' -message 'Updated!'`
end

on_stylesheet_error do |filename, message|
	_, line, samefile, file, message = */^\(Line (\d+)( of (.+?))?: (.+)$/.match(message)

	filename = file if samefile
	filebase = Shellwords.escape File.basename(filename)
	message = Shellwords.escape message

	filename = File.expand_path('./' + sass_dir + filename)

	`terminal-notifier -title 'Compass: #{filebase}' -subtitle 'ERROR: #{line}' -message #{message} -execute 'subl -a #{filename}:#{line}'`
end