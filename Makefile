clean:
	sudo rm -rf temp/cache/*
	sudo rm -rf log/*
pack:
	zip -r xherma33_src.zip app config log temp test vendor www composer.json composer.lock f136058.sql README.md web.config

.PHONY: clean
