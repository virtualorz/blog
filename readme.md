# Installation #

### install by composer ###
<pre><code>
composer require virtualorz/blog
</code></pre>

### edit config/app.php ###
<pre><code>
'providers' => [
    ...
    Virtualorz\Blog\BlogServiceProvider::class,
    Virtualorz\Pagination\PaginationServiceProvider::class,
    Virtualorz\Tag\TagServiceProvider::class,
    Virtualorz\Cate\CateServiceProvider::class,
    Virtualorz\Fileupload\FileuploadServiceProvider::class,
    ...
]

'aliases' => [
    ...
    'Blog' => Virtualorz\Blog\BlogFacade::class,
    'Pagination' => Virtualorz\Pagination\PaginationFacade::class,
    'Tag' => Virtualorz\Tag\TagFacade::class,
    'Cate' => Virtualorz\Cate\PCateFacade::class,
    'Fileupload' => Virtualorz\Fileupload\FileuploadFacade::class,
    ...
]
</code></pre>

### migration db table ###
<pre><code>
php artisan migrate
</code></pre>

### publish config ###
<pre><code>
php artisan vendor:publish --provider="Virtualorz\Blog\BlogServiceProvider"
</code></pre>


# usage #
#### 1. get cate list data ####
<pre><code>
$dataSet = Blog::list($param = []);
</code></pre>
$param['page'] : displaypage 


$param['is_backend'] : 1 to be backend list, show disabled data 


$param['app'] : use app def in config 


$param['use_sn] : use_sn define your self for defferent system use


$dataSet : return date

#### 2. add data to cate ####
<pre><code>
Contact::add($param = []);
</code></pre>
$param['app'] : use app def in config 


$param['use_sn] : use_sn define your self for defferent system use


with request variable name required : blog-cate_id,blog-title,blog-content

#### 3. get cate detail ####
<pre><code>
$dataRow = Contact::detail($blog_id);
</code></pre>

#### 4. edit data to cate ####
<pre><code>
Contact::edit($param = []);
</code></pre>
$param['app'] : use app def in config 


$param['use_sn] : use_sn define your self for defferent system use


with request variable name required : blog-cate_id,blog-title,blog-content

#### 5. delete cate data ####
<pre><code>
Contact::delete();
</code></pre>
with request variable name required : id as integer or id as array

#### 6. enable cate data ####
<pre><code>
Tag::enable($type);
</code></pre>
with request variable name required : id as integer or id as array
$type is 0 or1 , 0 to disable i to enable




