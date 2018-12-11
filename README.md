Os2Display admin for Københavns Kommune
===

# Bundles
## kkos2-display-bundle
This bundle contains slides specific to Københavns Kommune.

### "Mellemfeed"
This is a temporary data source used. On a KK multisite, create a "Servicesituation" node and use markup like this to include content:
```html
<blockquote>
<h2>SERVICESPOTS</h2>

<ol>
	<li>https://kibuk.testkkms.kk.dk/indhold/bloeb</li>
	<li>https://kibuk.testkkms.kk.dk/indhold/jep</li>
</ol>
</blockquote>

<blockquote>
<p>&nbsp;</p>

<h2>Plakater</h2>

<ol>
	<li>https://kulturhusetislandsbrygge.kk.dk/event/dans-paa-bryggen-21</li>
	<li>https://kulturhusetislandsbrygge.kk.dk/event/froeken-frika-jul-i-tivoliet</li>
	<li>https://kulturhusetislandsbrygge.kk.dk/event/vinterjazz-malene-kjaergaard-group</li>
</ol>
</blockquote>
```

The scraper-crawler ignores all things not in a blockquote, so feel free to add help text and the like.

### Gulpfile
There are a number of targets in the gulpfile right now. We use the docker setup from [https://github.com/kkos2/os2display-infrastructure](https://github.com/kkos2/os2display-infrastructure), but right now npm commands are just run on the developers local machine. That is not ideal and should be fixed in the future. Take a look in the file before you start touching front end code to get an idea of what goes where.

# Tagging for a release
The infrastructure that runs this is in [https://github.com/kkos2/os2display-infrastructure](https://github.com/kkos2/os2display-infrastructure). Up the number on the `build-x` tag to `build-2` for instance. Push that and then in the infrastructure repo, edit `provisioning/kubernetes-manifests/admin/deployment.yaml` and bump the tag number in the admin image. Use the makefile to build the release: `make build-release TAG=build-x` and then push it with `make push-release TAG=build-x`.