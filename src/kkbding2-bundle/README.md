# DING2 integration Bundle

## Setup
Add the following parameters to parameters.yml to enable integration and 
configure how to identify opening hours and service-intervals.:

<pre>
      ding_url: 'http://bibliotek.kk.dk'
      ding_opening_hours_category:
        libraryservice: 14291
        citizenservices: 14292  
</pre>

The values for the `libraryservice` and `citizenservices` keys are Drupal term
identifies for Opening Hours taxonomy terms. Consult an administrator for the 
DDB CMS installation to get the ids.  
