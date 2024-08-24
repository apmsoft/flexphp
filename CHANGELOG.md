# Changelog

## [1.3]

### - 2024-08-24
- ** Encrypt,Decrypt Deprecated
- 암호화 관련 AES256,Base64,Hash(256,512),PasswordHash,ROT13Encoder 클래서 암호화변 분리 생성 및 CipherGeneric 클래스 추가
- Random class 0.5-> 0.7 업데이트
- md5 관련 암호화 알고리즘 전체 제거 및 변경
- Random 관련 클래스들 0.7 버전에 으로 업그레이드

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

