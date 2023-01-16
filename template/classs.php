<?php
namespace Flex\My\BBS;
# classes/my/bbs

use Flex\Annona\App;
use Flex\Annona\Log;
use Flex\Annona\R;

use Flex\Annona\Db\DbMySqli;
use Flex\Annona\Db\WhereHelper;
use Flex\Annona\Request\Request;
use Flex\Annona\Request\FormValidation;
use Flex\Annona\Model;
use Flex\Annona\Paging\Relation;
use Flex\Annona\Date\DateTimez;
use Flex\Annona\Date\DateTimezPeriod;

# classes/my/bbs/Notice.class.php
class Notice 
{
    private $db;

    public function __construct(){
        $this->db = new \Flex\Annona\Db\DbMySqli();
    }

    public function doList()
    {
        # request
        $request = (object)(new Request())->get()->fetch();

        # Form Validation
        try{
            (new FormValidation('page','페이지',$request->page))->number();
            (new FormValidation('q','검색어',$request->q))->disliking(['-','.']);
        }catch(\Exception $e){
            Log::e($e->getMessage());
            return json_decode($e->getMessage(),true);
        }

        # resource
        R::tables();
        R::array();

        # Model
        $model = new Model();
        $model->total_record = 0;
        $model->page         = $request->page ?? 1;
        $model->page_count   = 10;
        $model->block_limit  = 2;
        $model->data         = [];

        # total record
        $model->total_record = $this->db->table(R::tables('member'))->total();

        # pageing
        $paging = new \Flex\Annona\Paging\Relation($model->total_record, $model->page);
        $relation = $paging->query( $model->page_count, $model->block_limit)->build()->paging();

        # query
        $rlt = $this->db->table(R::tables('member'))->select('id','name','userid','cellphone','signdate')->where(
            (new WhereHelper)->
                begin('OR')
                    ->case('name','LIKE',$request->q)
                    ->case('userid','LIKE',$request->q)
                    ->case('cellphone','LIKE',$request->q)
                ->end()
            ->where
        )->orderBy('id desc')->limit($paging->qLimitStart, $paging->qLimitEnd)->query();

        $article = ($model->total_record - $paging->pageLimit * ($paging->page - 1) ); // 순번
        while($row = $rlt->fetch_assoc())
        {
            # loop model
            $loop = new Model( $row );

            # 순번 추가
            $loop->num = $article;

            # 등록일
            $period = (new DateTimezPeriod())->diff(date('Y-m-d H:i:s'), $loop->signdate, ["format"=>'top']);
            $snsf   = explode(' ', $period);
            $data->signdate = match($snsf[1]) {
                'second','seconds' => sprintf("%d 초전",$snsf[0]),
                'minute','minutes' => sprintf("약%d 분전",$snsf[0]),
                'hour','hours'     => sprintf("약%d 시간전",$snsf[0]),
                'day','days'       => sprintf("약%d 일전",$snsf[0]),
                'month','months'   => sprintf("약%d 개월전",$snsf[0]),
                default            => $data->signdate
            };

            # data 담기
            $model->{"data+"} = $loop->fetch();
        $article--;
        }

        #r
        $r = R::select(['array'=>"is_push,random_params"]);

        # output
        return [
            "result"       => 'true',
            "r" => $r,
            'total_page'   => $paging->totalPage,
            'total_record' => $paging->totalRecord,
            'page'         => $paging->page,
            'paging'       => $relation,
            "msg"          => $model->data
        ];
    }

    public function doPost()
    {
        # request
        $request = (object)(new Request())->get()->fetch();

        # resource
        R::array();

        #r
        $r = R::select(['array'=>"is_push,random_params"]);

        # output
        return [
            "result" => 'true',
            "r" => $r,
            "msg"    => [
                'extract_id' => (new TokenGenerateAtype( null,10 ))->generateHashKey('md5')->value;
            ]
        ];
    }

    public function doInsert()
    {
        # resource
        R::tables();

        # request
        $request = (object)(new Request())->post()->fetch();

        # Form Validation
        try{
            (new FormValidation('name','이름',$request->name))->null()->disliking([]);
            (new FormValidation('email','이메일',$request->email))->null()->space()->email();
            (new FormValidation('extract_id','토큰',$request->extract_id))->null()->disliking([]);
        }catch(\Exception $e){
            Log::e($e->getMessage());
            return json_decode($e->getMessage(),true);
        }

        # insert
        $this->db->autocommit(FALSE);
        try{
            $this->db['name']       = $request->name;
            $this->db['email']      = $request->email;
            $this->db['extract_id'] = $request->extract_id;
            $this->db['signdate']   = (new DateTimez("now"))->format('Y-m-d H:i:s');
            $this->db->table(R::tables('member'))->insert();
        }catch(\Exception $e){
            Log::e($e->getMessage());
        }
        $this->db->commit();

        # output
        return [
            "result" => 'true',
            "msg"    => R::sysmsg('v_insert')
        ];
    }

    public function doView()
    {
        # request
        $request = (object)(new Request())->get()->fetch();

        # Form Validation
        try{
            (new FormValidation('id','식별번호',$request->id))->null()->number();
        }catch(\Exception $e){
            Log::e($e->getMessage());
            return json_decode($e->getMessage(),true);
        }

        # resource
        R::tables();
        R::array();

        # 데이터 체크
        $data = (object) $this->db->table(R::tables('test'))->where('id',$request->id)->query()->fetch_assoc();
        if(!isset($data->id)){
            return ["result"=>"false","msg_code"=>"e_db_unenabled","msg"=>R::sysmsg('e_db_unenabled')];
        }

        #r
        $r = R::select(['array'=>"is_push,random_params"]);

        # output
        return [
            "result" => 'true',
            'r'      => $r,
            "msg"    => $data->fetch()
        ];
    }

    public function doEdit()
    {
        # request
        $request = (object)(new Request())->get()->fetch();

        # Form Validation
        try{
            (new FormValidation('id','식별번호',$request->id))->null()->number();
        }catch(\Exception $e){
            Log::e($e->getMessage());
            return json_decode($e->getMessage(),true);
        }

        # resource
        R::tables();
        R::array();

        # 데이터 체크
        $data = (object) $this->db->table(R::tables('test'))->where('id',$request->id)->query()->fetch_assoc();
        if(!isset($data->id)){
            return ["result"=>"false","msg_code"=>"e_db_unenabled","msg"=>R::sysmsg('e_db_unenabled')];
        }

        #r
        $r = R::select(['array'=>"is_push,random_params"]);

        # output
        return [
            "result" => 'true',
            'r'      => $r,
            "msg"    => $data->fetch()
        ];
    }

    public function doUpdate()
    {
        # request
        $request = (object)(new Request())->post()->fetch();

        # resource
        R::tables();

        # Form Validation
        try{
            (new FormValidation('id','식별번호',$request->id))->null()->number();
            (new FormValidation('name','이름',$request->name))->null()->disliking([]);
            (new FormValidation('email','이메일',$request->email))->null()->space()->email();
        }catch(\Exception $e){
            Log::e($e->getMessage());
            return json_decode($e->getMessage(),true);
        }

        # 데이터 체크
        $data = (object) $this->db->table(R::tables('test'))->where('id',$request->id)->query()->fetch_assoc();
        if(!isset($data->id)){
            return ["result"=>"false","msg_code"=>"e_db_unenabled","msg"=>R::sysmsg('e_db_unenabled')];
        }

        # update
        $this->db->autocommit(FALSE);
        try{
            $this->db['name']     = $request->name;
            $this->db['email']    = $request->email;
            $this->db->table(R::tables('test'))->where('id',$request->id)->update();
        }catch(\Exception $e){
            Log::e($e->getMessage());
        }
        $this->db->commit();

        # output
        return [
            "result" => 'true',
            "msg"    => R::sysmsg('v_update')
        ];
    }

    public function doDelete()
    {
        # request
        $request = (object)(new Request())->post()->fetch();

        # Form Validation
        try{
            (new FormValidation('id','식별번호',$request->id))->null()->number();
        }catch(\Exception $e){
            Log::e($e->getMessage());
            return json_decode($e->getMessage(),true);
        }

        # resource
        R::tables();

        # 데이터 체크
        $data = (object) $this->db->table(R::tables('test'))->where('id',$request->id)->query()->fetch_assoc();
        if(!isset($data->id)){
            return ["result"=>"false","msg_code"=>"e_db_unenabled","msg"=>R::sysmsg('e_db_unenabled')];
        }

        # update
        $this->db->autocommit(FALSE);
        try{
            $this->db->table(R::tables('test'))->where('id',$request->id)->delete();
        }catch(\Exception $e){
            Log::e($e->getMessage());
        }
        $this->db->commit();

        # output
        return [
            "result" => 'true',
            "msg"    => R::sysmsg('v_delete')
        ];
    }
}
?>