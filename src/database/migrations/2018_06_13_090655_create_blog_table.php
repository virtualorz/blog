<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBlogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blog', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->integer('cate_id')->unsigned()->nullable()->comment('分類ID');
            $table->string('app', 12)->comment('應用 : new,pic,video,blog,activity');
            $table->string('use_sn', 12)->comment('使用套件SN(vendor)');
            $table->dateTime('created_at')->comment('建立資料時間');
            $table->dateTime('updated_at')->comment('最後編輯資料時間');
            $table->text('photo')->comment('圖片');
            $table->string('youtube', 128)->comment('youtube影片');
            $table->string('title', 64)->comment('標題');
            $table->text('introduction')->comment('簡介');
            $table->text('content')->comment('內文');
            $table->text('content_zip')->nullable()->comment('內文zip上傳');
            $table->text('files')->comment('附件');
            $table->bigInteger('view_count')->unsigned()->comment('瀏覽人次');
            $table->tinyInteger('top')->unsigned()->comment('0:正常 1:置頂');
            $table->tinyInteger('enable')->unsigned()->comment('0:停用 1:啟用');
            $table->dateTime('delete')->nullable()->comment('刪除時間');
            $table->integer('creat_admin_id')->unsigned()->nullable()->comment('建立資料管理員ID');
            $table->integer('update_admin_id')->unsigned()->nullable()->comment('最夠更新資料管理員ID');
        });
        Schema::create('blog_lang', function (Blueprint $table) {
            $table->bigInteger('blog_id')->unsigned()->comment('部落格ID');
            $table->string('lang',3)->comment('語言');
            $table->dateTime('created_at')->comment('建立資料時間');
            $table->dateTime('updated_at')->comment('最後編輯資料時間');
            $table->text('photo')->comment('圖片');
            $table->string('youtube', 128)->comment('youtube影片');
            $table->string('title', 64)->comment('標題');
            $table->text('introduction')->comment('簡介');
            $table->text('content')->comment('內文');
            $table->text('content_zip')->nullable()->comment('內文zip上傳');
            $table->text('files')->comment('附件');
            $table->integer('creat_admin_id')->unsigned()->nullable()->comment('建立資料管理員ID');
            $table->integer('update_admin_id')->unsigned()->nullable()->comment('最夠更新資料管理員ID');
            $table->primary(['blog_id', 'lang']);
        });
        Schema::create('blog_tag', function (Blueprint $table) {
            $table->bigInteger('blog_id')->unsigned()->comment('部落格ID');
            $table->integer('tag_id')->comment('標籤ID');
            $table->integer('creat_admin_id')->unsigned()->nullable()->comment('建立資料管理員ID');
            $table->integer('update_admin_id')->unsigned()->nullable()->comment('最夠更新資料管理員ID');
            $table->primary(['blog_id', 'tag_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('blog');
        Schema::dropIfExists('blog_lang');
        Schema::dropIfExists('blog_tag');
    }
}
