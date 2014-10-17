Template setup
=============

### Template setup
[link](IK3-templates.pdf)


### Files to include in your template
* **(name).html**  The html displayed in the client
* **(name)-preview.html**  The html displayed in the backend (i.e overview lists etc.)
* **(name)-edit.html**  The html displayed in the backend with edit links included.
* **(name).scss**  An optional sass file to render into the css file.
* **(name).css**  The actual styles related to all the html files.
* **(name).png**  An image to represent what kind of template the we are dealing with.
* **(name).js**  Optional custom javascript related specifically to the template (e.g . Sliding through several images)
* **(name).json**  The configuration settings for the slide.


### Naming
* All templates should be included in the shared ik-templates folder.
* All files related to a template should be contained in the same template folder.
* The folder name of a template folder should consist of only letters and dashes.
* The optional sass file should be prefixed with underscore.
* All other files within the folder should be named: folder-name.type (e.g manual-calendar.html)


**Folder structure and naming example**

web

-- ik-templates

---- template-1

------ template-1.html

------ template-1-preview.html

------ template-1-edit.html

------ template-1.css

------ _template-1.scss

------ template-1.json

------ template-1.png

------ template-1.js

---- text-bottom

------ text-bottom.html

------ text-bottom-preview.html

------ text-bottom-edit.html

------ text-bottom.css

------ _text-bottom.scss

------ text-bottom.json

------ text-bottom.png

------ text-bottom.js