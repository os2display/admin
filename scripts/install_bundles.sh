#!/usr/bin/env bash
dir=$(cd $(dirname "${BASH_SOURCE[0]}")/../ && pwd)

develop=false
for arg in $*; do
	case $arg in
		--dev)
			develop=true
			;;
	esac
done

get_bundles() {
	if [ $develop = true ]; then
			bundles_dir=$dir/../bundles/
			mkdir -p $bundles_dir/$name
			cd $bundles_dir/$name
			for bundle in ${bundles[@]}; do
				tokens=(${bundle//@/ })
				repo=${tokens[0]}
				branch=${tokens[1]:-master}
				echo $name/$repo@$branch
				if [ ! -d $repo ]; then
						git clone https://github.com/$name/$repo.git
				fi
				git -C $repo fetch --quiet
				git -C $repo checkout $branch --quiet
			done
	fi
}

config_composer() {
	cd $dir
	for bundle in ${bundles[@]}; do
		tokens=(${bundle//@/ })
		repo=${tokens[0]}
		branch=${tokens[1]:-master}
		if [ $develop = true ]; then
			composer config repositories.$name/$repo path ../bundles/$name/$repo
		else
			composer config repositories.$name/$repo vcs https://github.com/$name/$repo
		fi
	done
}

name=itk-os2display
bundles=(
		aarhus-data-bundle@1.1.1
		aarhus-second-template-bundle@1.0.4
		admin-bundle@1.0.13
		campaign-bundle@develop
		core-bundle@1.0.14
		default-template-bundle@1.0.8
		exchange-bundle@1.0.1
		horizon-template-bundle@1.0.3
		lokalcenter-template-bundle@1.0.5
		media-bundle@1.0.2
		template-extension-bundle@1.1.11
		vimeo-bundle@1.0.1
		os2display-koba-integration@1.0.5
)

get_bundles
config_composer

name=aakb
bundles=(
	os2display-aarhus-templates@1.0.15
)

get_bundles
config_composer
