DAYS = $(sort $(wildcard day$(day)*/))

run:
	@for I in $(DAYS); do \
		echo "$$I\n====="; \
		$(MAKE) --no-print-directory -C $$I run; \
		echo " "; \
	done

test:
	@for I in $(DAYS); do \
		$(MAKE) --no-print-directory -C $$I test; \
	done

clean:
	@for I in $(DAYS); do \
		$(MAKE) --no-print-directory -C $$I clean; \
	done

