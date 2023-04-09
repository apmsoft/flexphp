<?php 
namespace Flex\Components;

use Flex\Annona\R;
use Flex\Annona\Log;

use Flex\Components\DataProcessing;
use Flex\Components\Validation;

abstract class ListActivityAbstract extends Activity
{
    protected Model $model;

    public function __construct(
        private string $Table,
        private array $request
    ){
        parent::__construct($Table);
        
        self::init();
    }

    # 데이터기본셋팅 및 초기화
    public function init (): ListActivityAbstract 
    {
        $this->model->result       = "true";
        $this->model->total_record = 0;
        $this->model->page         = (isset($this->request['page'])) ? $this->request['page'] : 1;
        $this->model->page_count   = (isset($this->request['page_count'])) ? $this->request['page_count'] : 10;
        $this->model->block_limit  = (isset($this->request['block_limit'])) ? $this->request['block_limit'] : 5;
        $this->model->q            = (isset($this->request['q'])) ? $this->request['q'] : '';
        $this->model->paging       = [];
        $this->model->orderBy      = 'id DESC';
        $this->model->msg          = [];
    return $this;
    }

    # 밸리데이션 체크
    protected function validation () : ListActivityAbstract 
    {
        $validation = new Validation();
        foreach($this->request as $key => $v){
            $validation->is($key, $v);
        }
    return $this;
    }

    # 총데이터레코드 수
    protected function totalRecord (string $where) : ListActivityAbstract
    {
        # total record
        $this->model->total_record = parent::table( $this->Table )->where($where)->total();

    return $this;
    }

    # 페이징 채널 생성
    protected function pageRelation () : ListActivityAbstract 
    {
        # pageing
        $paging = new Relation($this->model->total_record, $this->model->page);
        $this->model->paging = $paging->query( $this->model->page_count, $this->model->block_limit)->build()->paging();
        $this->model->total_page   = $paging->totalPage;
        $this->model->total_record = $paging->totalRecord;
    return $this;
    }

    # while 쿼리
    protected function runQuery(params $columns, string $where) : ListActivityAbstract 
    {
        # while
        $result = $this->db->table( $this->Table )
            ->select($columns)
            ->where($where)
            ->orderBy($this->model->orderBy)
            ->limit($this->model->paging->qLimitStart,$this->model->page_count)
        ->query();
        while($row = $result->fetch_assoc()) 
        {
            $dataProcessing = new DataProcessing($row);
            foreach($row as $column_name => $column_value){
                match($column_name){
                    ColumnsEnum::TITLE->value => $dataProcessing->put(ColumnsEnum::TITLE->name, $column_value, "cut", [60])
                    ColumnsEnum::DESCRIPTION->value => $dataProcessing->put(ColumnsEnum::DESCRIPTION->name, $column_value, "cut", [200])
                };
            }

            $this->model->{"msg+"} = $dataProcessing->fetchAll();

        }
    return $this;
    }

    # 출력값 정의
    protected function putOutData (array $params) : ListActivityAbstract 
    {
        $this->model = new Model(array_merge($this->model->fetch(), $params));
    return $this;
    }

    # 데이터 리턴
    public function extract() : array
    {
        return $this->model->fetch();
    }

    #@ abstract
    public function doList() : array;
}
?>