# Changelog

## [2.5.0]


### - 2024-10-09
- composer.json 설정
- composer 규격에 따른 클래스명에 .class 를 제거
- components 폴더명 및 패키지명 대거 수정

### - 2024-10-09
- util/불필요한 클래스 파일 삭제
- 불필요한 test 파일 삭제

### - 2024-10-08
- annona/db/DbMySqli.class.php v2.2.4 decrypt 버그수정

### - 2024-10-07
- annona/db/DbMySqli.class.php v2.2.3 decrypt 버그수정

### - 2024-10-03
- annona/db/DbMySqli.class.php v2.2.1 decrypt REGEXP 에서 DATE_TYPE 으로 변경 속도 향상

### - 2024-10-02
- annona/db/DbMySqli.class.php v2.2.0 으로 버전업
-- selectCrypt 숫자 데이터는 decrypt 하지 않도록 업데이트 퀄럼 기반이 아닌 데이터 값 기준

### - 2024-09-30
- DoInterface.class new add

### - 2024-09-26
- annona/token/*.class update

### - 2024-09-25
- util/Requested.class v1.0 reactphp ServerRequestInterface 전체 기능을 사용할 수 있도록 확장

### - 2024-09-24
- annona/file/Upload.class v2.2.3 버그 패치
- annona/file/Upload.class v2.2.2 reactphp request, _FILES 관련 호환성 업데이트
- annona/file/Upload.class 파일명 암호화 Deprecated 관련 패치 및 v2.2 업데이트
- annona/file/*.class 자잘한 코딩 패치 작업

### - 2024-09-13
- HttpRequest class v1.1.0 update 
    - post 첨부파일 포함해서 전송할 수 있도록 업데이트

### - 2024-09-11
- ColumnsTypes 예제 업데이트

### - 2024-09-04
- ColumnsTypes Columns 으로 이동 및 패키지명 변경
- Components\Data\Action ReplyInterface.class 파일명 수정
- test/columns_types 예제파일 수정
- FormValidation v2.2 클래스에 when 조건문을 작성할 수 있는 메소드 추가 when이 true 일때만 다음 체크 메소드들을 실행함
- EntryArrayTrait, EnumInstanceTrait, EnumValueInsterface, EnumValueStorage update && new add
- Enum Trait Example : ExampleEnum, ExampleTypesTrait class new add
- test/columns_types.php 예제 업데이트

- components/mgmt/ 컴포넌트 trait 클래스 변경 및 추가
    -- FidProviderInterface, FidTrait
    -- ImageCompressorInterface, ImageCompressorBase64Trait, ImageComporessorEditjsTrait
    -- test/imag_base64.php 예제파일

### - 2024-09-03
- ArrayHelper class v1.3.2 업데이트 map,reduce,__set 메소드 기능 추가, extractValues -> pluck 으로 메소드명 변경

### - 2024-09-02
- BaseAdapterInterface 추가
- BaseAdapter BaseAdapterInterface 구현하도록 명시 및 Relation 클래스 제거

### - 2024-08-29
- WhereHelper 클래스 업데이트 WhereHelperInterface 를 통해 확장 가능성 높임
- DbBaseAdapter 클래스에 WhereHelper 클래스 자동 선언 및 WhereHelper를 상속받은 CustumWhereHelper 클래스 등록 할 수 있도록 기능 추가
- WhereHelper v2.0 업데이트
- *DbBaseAdapter 클래스명을 DbMysqlAdapter 로 변경 다른 데이터베이스로도 확장하며 어떤 디비용인지 명확하게 하기 위함

### - 2024-08-28
- 디비데이터에 따른 타입 지정 샘플 코드 추가
- Adapter 개념 도입

### - 2024-08-27
- HashEncoder 와 Base64UrlEncoder 상속저의 해제
- components/data/action 좀더 세세하게 interface 분리 및 결함

### - 2024-08-26
- ArrayHelper ver1.3.1 버그 패치 
- Encrypt, Decrypt Class Deprecated
- TokenGenerateBtype, TokenGenerateAtype 토큰화 클래스 v1.1 ->v1.2 업데이트
- Cipher* 클래스들 버전 명시 v1.0

### - 2024-08-24
- ** Encrypt,Decrypt Deprecated
- 암호화 관련 AES256,Base64,Hash(256,512),PasswordHash,ROT13Encoder 클래서 암호화변 분리 생성 및 CipherGeneric 클래스 추가
- Random class 0.5-> 0.7 업데이트
- md5 관련 암호화 알고리즘 전체 제거 및 변경
- Random 관련 클래스들 0.7 버전에 으로 업그레이드
- ROT13Encoder class update

### - 2024-08-09
- ImageViewer 버그 패치
- Calendars 클래스 버그 패치

### - 2024-07-23
- Model class v2.0 업데이트
- Model class 기존 복잡한 방식 버리고 사용자 php 코딩 스타일 일치 시킴

### - 2024-07-18
- ArrayHelper class v1.3 업데이트
- ArrayHelper select 메소드 추가 : 원하는 키로만 배열을 구성하는 기능

### - 2024-07-12
- ArrayHelper class v1.2 업데이트
 -  빈데이터가 있는 배열 찾기 
    -- isnull(...$params) null 및 빈값 찾기, 특정한 키에만 null 또는 빈값이 있는지 찾기 기능 추가
 -  빈데이터가 있는 배열 제거 
    -- dropnull(...$params) null 및 빈값 제거, 특정한 키에만 null 또는 빈값이 있는지 찾아 제거 기능 추가

### - 2024-07-04
- WhereHelper class v1.8 업데이트
 - end() 함수 선언하지 않아도 자동으로 닫기 기능 실행 
 - 전체 그룹을 AND 또는 OR 로 묶음할 수 있는 기능 추가

### - 2024-06-27
- Requested util class update
- UploadProcess util class update

### - 2024-06-26
- ImageGDS.class image base64 소스 크기 읽기 쓰기 크기리사이즈 기능 추가

## [1.2]
### - 2024-06-19
- JsonEncoder 클래스 추가 jsonString 으로 변환하되 numberic 타입은 numberic 타입으로 자동 형변화 기능
- 컴포넌트 데이터 액션 인터페이스 클래스 리턴 타입 array에서 string 으로 전환

### - 2024-05-09
- HttpRequest v1.0.2 -> v1.0.3, set 함수 추가 (생성자에서 배열로 등록해야만 했던 요청 주소및파라메터들을 쉽게 등록할 수 있도록 업데이트)

