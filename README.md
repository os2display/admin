# Aroskanalen administrator interface

For general information about installation see the https://github.com/aroskanalen/docs/blob/development/Installation%20guide.md in the docs repository.

# Information
When working with aroskanalen together with the vagrant provide. You have to visit screen.indholdskanalen.vm, search.indholdskanalen.vm, middleware.indholdskanalen.vm, admin.indholdskanalen.vm and accept the self-sign certificates. If you don't open a tab for each in Chrome, if not it will not work.

# Helpful commands
We have defined a couple of commands for Indholdskanalen.

To push content
<pre>
php app/console ik:push
</pre>

To reindex search
<pre>
php app/console ik:reindex
</pre>
This does not include delete of records that are removed from symfony but not search.

To clear cache
<pre>
php app/console cache:clear
</pre>

To brute force clear cache
<pre>
rm -rf app/cache/*
</pre>

