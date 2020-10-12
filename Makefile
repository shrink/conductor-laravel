MAKEFLAGS += --silent

COMPOSER_COMMAND = docker-compose run --rm validation composer

# Run the library's code quality checks (test, analysis)
.PHONY: check
check:
	${COMPOSER_COMMAND} check

# Run the library's tests
.PHONY: test
test:
	${COMPOSER_COMMAND} test

# Tag a new version of the library | VERSION!
.PHONY: version
version:
	test -n "$(VERSION)"
	git show --oneline -s
	read -p "Are you sure you want to force tag v$(VERSION)? Y [enter] / N [ctrl]+[c]"
	git tag -fsam ':gift: Version $(VERSION)' v$(VERSION) && \
	git push -f origin v$(VERSION)

# Log in to the development container
.PHONY: shell
shell:
	docker-compose run --rm validation sh

# List supported commands
.PHONY: help
help:
	@echo "\`make help\` is not supported by native Make"
	@echo "Download Modern Make to gain access to a dynamic command list"
	@echo "\033[92m»»\033[0m https://github.com/tj/mmake"
