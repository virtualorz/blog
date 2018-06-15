<?php
namespace Virtualorz\Blog;

use DB;
use Request;
use Validator;
use Fileupload;
use App\Exceptions\ValidateException;
use PDOException;
use Exception;
use Pagination;
use Config;

class Blog
{
    public function list($param = []) {

        $page_display = intval(Request::input('page_display', 10));
        if (!in_array($page_display, Config::get('pagination.data_display', []))) {
            $page_display = Config::get('pagination.items');
        }
        $dataSet = collect();
        $def_param = [
            'page' => 0,
            'is_backend' => 0,
            'app' => '',
            'use_sn' => '',
        ];
        $param = array_merge($def_param,$param);

        if(in_array($param['app'],config('blog.app')))
        {
            $qb = DB::table('blog')
                ->select([
                    'blog.id',
                    'blog.created_at',
                    'blog.title',
                    'blog.view_count',
                    'blog.enable',
                    'cate.name as cate_name'
                ])
                ->leftJoin('cate','blog.cate_id','=','cate.id')
                ->whereNull('blog.delete')
                ->where('blog.app',$param['app'])
                ->where('blog.use_sn',$param['use_sn']);
            if($param['is_backend'] == 0)
            {
                $qb->where('enable',1);
            }
            if($param['page'] !== 0)
            {
                $qb->offset(($param['page'] - 1) * $page_display)
                    ->limit($page_display);
            }
            $dataSet = $qb->get();

            //多語言處理
            foreach($dataSet as $k=>$v)
            {
                $dataSet_lang = DB::table('blog_lang')
                    ->select([
                        'blog_lang.lang',
                        'blog_lang.created_at',
                        'blog_lang.title',
                    ])
                    ->where('blog_lang.blog_id',$v->id)
                    ->get()
                    ->keyBy('lang');
                $dataSet[$k]->lang = $dataSet_lang;
            }
            $dataCount = $qb->cloneWithout(['columns', 'orders', 'limit', 'offset'])
                    ->cloneWithoutBindings(['select', 'order'])
                    ->count();
                
            Pagination::setPagination(['total'=>$dataCount]);
        }

        

        return $dataSet;
    }

    public function add($param = [])
    {
        $def_param = [
            'app' => '',
            'use_sn' => '',
        ];
        $param = array_merge($def_param,$param);

        if(in_array($param['app'],config('blog.app')))
        {
            $validator = Validator::make(Request::all(), [
                'blog-cate_id' => 'integer|required',
                'blog-title' => 'string|required|max:64',
                'blog-content' => 'string|required|',
            ]);
            if ($validator->fails()) {
                throw new ValidateException($validator->errors());
            }
    
            foreach (Request::input('blog-lang', []) as $k => $v) {
                $validator = Validator::make($v, [
                    'blog-title' => 'string|required|max:64',
                    'blog-content' => 'string|required',
                ]);
                if ($validator->fails()) {
                    throw new ValidateException($validator->errors());
                }
            }

            $dtNow = new \DateTime();

            DB::beginTransaction();
            try {

                $insert_id = DB::table('blog')
                    ->insertGetId([
                        'cate_id' => Request::input('blog-cate_id'),
                        'app' => $param['app'],
                        'use_sn' => $param['use_sn'],
                        'created_at' => $dtNow,
                        'updated_at' => $dtNow,
                        'photo' => Request::input('blog-photo','[]'),
                        'youtube' => Request::input('blog-youtube',''),
                        'title' => Request::input('blog-title',''),
                        'introduction' => Request::input('blog-introduction',''),
                        'content' => Request::input('blog-content',''),
                        'files' => Request::input('blog-files','[]'),
                        'view_count' => 0,
                        'top' => Request::input('blog-top',0),
                        'enable' => Request::input('blog-enable'),
                        'creat_admin_id' => Request::input('blog-creat_admin_id', null),
                        'update_admin_id' => Request::input('blog-update_admin_id', null),
                    ]);
                
                foreach (Request::input('blog-lang', []) as $k => $v) {
                    DB::table('blog_lang')
                        ->insert([
                            'blog_id' => $insert_id,
                            'lang' => $k,
                            'created_at' => $dtNow,
                            'updated_at' => $dtNow,
                            'photo' => (isset($v['blog-photo'])) ? $v['blog-photo'] : '[]',
                            'youtube' => (isset($v['blog-youtube'])) ? $v['blog-youtube'] : '',
                            'title' => (isset($v['blog-title'])) ? $v['blog-title'] : '',
                            'introduction' => (isset($v['blog-introduction'])) ? $v['blog-introduction'] : '',
                            'content' => (isset($v['blog-content'])) ? $v['blog-content'] : '',
                            'files' => (isset($v['blog-files'])) ? $v['blog-files'] : '[]',
                            'creat_admin_id' => Request::input('blog-creat_admin_id', null),
                            'update_admin_id' => Request::input('blog-update_admin_id', null),
                        ]);
                }

                DB::commit();

            } catch (\PDOException $ex) {
                DB::rollBack();
                throw new PDOException($ex->getMessage());
                \Log::error($ex->getMessage());
            } catch (\Exception $ex) {
                DB::rollBack();
                throw new Exception($ex);
                \Log::error($ex->getMessage());
            }

        }
        else
        {
            throw new Exception('app error');
        }
    }

    public function edit($param = [])
    {
        $def_param = [
            'app' => '',
            'use_sn' => '',
        ];
        $param = array_merge($def_param,$param);

        if(in_array($param['app'],config('blog.app')))
        {
            $validator = Validator::make(Request::all(), [
                'blog-cate_id' => 'integer|required',
                'blog-title' => 'string|required|max:64',
                'blog-content' => 'string|required|',
            ]);
            if ($validator->fails()) {
                throw new ValidateException($validator->errors());
            }
    
            foreach (Request::input('blog-lang', []) as $k => $v) {
                $validator = Validator::make($v, [
                    'blog-title' => 'string|required|max:64',
                    'blog-content' => 'string|required',
                ]);
                if ($validator->fails()) {
                    throw new ValidateException($validator->errors());
                }
            }

            $dtNow = new \DateTime();

            DB::beginTransaction();
            try {
                
                DB::table('blog')
                    ->where('id', Request::input('id'))
                    ->where('use_sn',$param['use_sn'])
                    ->update([
                        'cate_id' => Request::input('blog-cate_id'),
                        'updated_at' => $dtNow,
                        'photo' => Request::input('blog-photo','[]'),
                        'youtube' => Request::input('blog-youtube',''),
                        'title' => Request::input('blog-title',''),
                        'introduction' => Request::input('blog-introduction',''),
                        'content' => Request::input('blog-content',''),
                        'files' => Request::input('blog-files','[]'),
                        'top' => Request::input('blog-top',0),
                        'enable' => Request::input('blog-enable'),
                        'update_admin_id' => Request::input('blog-update_admin_id', null),
                    ]);
                foreach (Request::input('blog-lang', []) as $k => $v) {
                    DB::table('blog_lang')
                        ->where('blog_id', Request::input('id'))
                        ->where('lang', $k)
                        ->update([
                            'updated_at' => $dtNow,
                            'photo' => (isset($v['blog-photo'])) ? $v['blog-photo'] : '[]',
                            'youtube' => (isset($v['blog-youtube'])) ? $v['blog-youtube'] : '',
                            'title' => (isset($v['blog-title'])) ? $v['blog-title'] : '',
                            'introduction' => (isset($v['blog-introduction'])) ? $v['blog-introduction'] : '',
                            'content' => (isset($v['blog-content'])) ? $v['blog-content'] : '',
                            'files' => (isset($v['blog-files'])) ? $v['blog-files'] : '[]',
                            'update_admin_id' => Request::input('blog-update_admin_id', null),
                        ]);
                }

                DB::commit();

            } catch (\PDOException $ex) {
                DB::rollBack();
                throw new PDOException($ex->getMessage());
                \Log::error($ex->getMessage());
            } catch (\Exception $ex) {
                DB::rollBack();
                throw new Exception($ex);
                \Log::error($ex->getMessage());
            }
        }
        else
        {
            throw new Exception('app error');
        }
    }

    public function detail($id = '')
    {
        $dataRow_blog = collect();
        try {
            $dataRow_blog = DB::table('blog')
                ->select([
                    'blog.id',
                    'blog.cate_id',
                    'blog.created_at',
                    'blog.updated_at',
                    'blog.photo',
                    'blog.youtube',
                    'blog.title',
                    'blog.introduction',
                    'blog.content',
                    'blog.files',
                    'blog.view_count',
                    'blog.top',
                    'blog.enable',
                    'blog.update_admin_id',
                    'cate.name AS cate_name',
                ])
                ->LeftJoin('cate','blog.cate_id','=','cate.id')
                ->where('blog.id', $id)
                ->whereNull('blog.delete')
                ->first();
            if ($dataRow_blog != null) {
                $dataSet_lang = DB::table('blog_lang')
                    ->select([
                        'blog_lang.lang',
                        'blog_lang.created_at',
                        'blog_lang.updated_at',
                        'blog_lang.photo',
                        'blog_lang.youtube',
                        'blog_lang.title',
                        'blog_lang.introduction',
                        'blog_lang.content',
                        'blog_lang.files',
                    ])
                    ->where('blog_lang.blog_id', $dataRow_blog->id)
                    ->get()
                    ->keyBy('lang');
                foreach($dataSet_lang as $k=>$v)
                {
                    $dataSet_lang[$k]->photo_link = head(Fileupload::getFiles($v->photo));
                    $dataSet_lang[$k]->files_link = head(Fileupload::getFiles($v->files));
                    $dataSet_lang[$k]->content = json_decode($v->content, true);
                }
                $dataRow_blog->lang = $dataSet_lang;
                $dataRow_blog->photo_link = head(Fileupload::getFiles($dataRow_blog->photo));
                $dataRow_blog->files_link = head(Fileupload::getFiles($dataRow_blog->files));
                $dataRow_blog->content = json_decode($dataRow_blog->content, true);
            }
        } catch (\PDOException $ex) {
            throw new PDOException($ex->getMessage());
            \Log::error($ex->getMessage());
        } catch (\Exception $ex) {
            throw new Exception($ex);
            \Log::error($ex->getMessage());
        }

        return $dataRow_blog;
    }

    public function delete()
    {
        $validator = Validator::make(Request::all(), [
            'id' => 'required', //id可能是陣列可能不是
        ]);
        if ($validator->fails()) {
            throw new ValidateException($validator->errors());
        }

        $ids = Request::input('id', []);
        if (!is_array($ids)) {
            $ids = [$ids];
        }

        $dtNow = new \DateTime();

        DB::beginTransaction();
        try {
            foreach ($ids as $k => $v) {

                DB::table('blog')
                    ->where('id', $v)
                    ->update([
                        'delete' => $dtNow,
                    ]);
            }

            DB::commit();
        } catch (\PDOException $ex) {
            DB::rollBack();
            throw new PDOException($ex->getMessage());
            \Log::error($ex->getMessage());
        } catch (\Exception $ex) {
            DB::rollBack();
            throw new Exception($ex);
            \Log::error($ex->getMessage());
        }
    }

    public function enable($type = '')
    {
        if ($type !== '') {
            $validator = Validator::make(Request::all(), [
                'id' => 'required', //id可能是陣列可能不是
            ]);
            if ($validator->fails()) {
                throw new ValidateException($validator->errors());
            }

            $ids = Request::input('id', []);
            if (!is_array($ids)) {
                $ids = [$ids];
            }

            $dtNow = new \DateTime();

            DB::beginTransaction();
            try {
                foreach ($ids as $k => $v) {
                    DB::table('blog')
                        ->where('id', $v)
                        ->whereNull('delete')
                        ->update([
                            'enable' => $type,
                            'updated_at' => $dtNow,
                        ]);
                }
                DB::commit();
            } catch (\PDOException $ex) {
                DB::rollBack();
                throw new PDOException($ex->getMessage());
                \Log::error($ex->getMessage());
            } catch (\Exception $ex) {
                DB::rollBack();
                throw new Exception($ex);
                \Log::error($ex->getMessage());
            }
        }
    }
}
