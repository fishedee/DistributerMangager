BASEPATH := `pwd`
OUT := ../build
DIST_OUT := ../dist
COMMON := ../../third-party/FishStrap/src
COMMON_OUT := fishstrap
COMMON_IN := `cd $(COMMON) && pwd`
.PHONY:copy_common debug release watch
all:
	$(MAKE) debug
copy_common:
	-rm $(COMMON_OUT) -rf
	mkdir $(COMMON_OUT)
	cd $(COMMON_OUT)
	for i in `cd $(COMMON) && find .`; do \
		if [ "$$i" != "." ];then \
			if [ -d $(COMMON_IN)/$$i ]; then \
				mkdir $(COMMON_OUT)/$$i;\
			else \
				ln $(COMMON_IN)/$$i $(COMMON_OUT)/$$i;\
			fi \
		fi \
	done
debug:copy_common
	-rm $(OUT) -rf
	fis release --md5 --domains --dest $(OUT)
release:copy_common
	-rm $(DIST_OUT) -rf
	fis release --md5 --pack --domains --optimize --dest $(DiST_OUT)
watch:debug
	fis release --md5 --domains --watch --dest $(OUT)
