Template setup
=============


### To add a new template
* figure out a name for your template, and namespace it with your organisation, e.g.:
<pre>
Name: my-template

Organisation: this-library

This results in the template name: this-library-my-template
</pre>

* make a new folder with the organization name, in web/templates/slides/

* make a folder in the organization folder with the template name.

* include the html, css, png and json files needed (see list below).

### Template setup
[link](IK3-templates.pdf)


### Files to include in your template
* **(name).html**  The html displayed in the client
* **(name)-preview.html**  The html displayed in the backend (i.e overview lists etc.)
* **(name)-edit.html**  The html displayed in the backend with edit links included.
* **(name).scss**  An optional sass file to render into the css file.
* **(name).css**  The actual styles related to all the html files.
* **(name).png**  An image to represent what kind of template the we are dealing with.
* **(name).json**  The configuration settings for the slide.


### Naming
* Important! All custom made templates should be namespaced with organisation name, so templates do not collide.
* All templates should be included in the shared web/templates/slides/ folder.
* All files related to a template should be contained in the same template folder.
* The folder name of a template folder should consist of only letters and dashes.
* All files within the folder should start with the folder name. (e.g. manual-calendar.html)


### Folder structure and naming example

**/web**

-- **/templates/slides/**

---- **/template-1**

------ template-1.html

------ template-1-preview.html

------ template-1-edit.html

------ template-1.css

------ _template-1.scss

------ template-1.json

------ template-1.png

------ template-1.js

---- **/text-bottom**

------ text-bottom.html

------ text-bottom-preview.html

------ text-bottom-edit.html

------ text-bottom.css

------ _text-bottom.scss

------ text-bottom.json

------ text-bottom.png

------ text-bottom.js