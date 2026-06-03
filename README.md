
# <p align=right>بِسْــــمِ اللّٰهِ الرَّحْمَـنِ الرَّحِيـمِ</p>

## Dataset Generator

**[TR]**
Dataset Generator; belirlenen formatta ve özelliklerde, çeşitli çıktı türlerini destekleyen, makine öğrenimi modelleri; veri analizi, test/demo ortamları ve araştırma projelerinde ihtiyac duyulan veri setlerinin üretimini kolaylaştırma amaçlı geliştirilen açık kaynak bir projedir.
<br></br>
**[ENG]**
Dataset Generator is an open-source project developed to facilitate the generation of datasets required for machine learning models, data analysis, test/demo environments, and research projects, supporting various output types in specified formats and with defined characteristics.

---

**Dil / Language:** [Türkçe](#turkce) | [English](#english)

---

  

<a  id="turkce"></a>
## [TR] 
## Açıklama
Dataset Generator hızlı ve kolay bir şekilde veri setleri oluşturmayı sağlayan açık kaynak kodlu bir araçtır. Proje iki ana bileşenden oluşur:
-  **API (Flask)** -- Veri seti üretimini sunucu bilgisayarda gerçekleştiren REST API servisi.

-  **Web Uygulaması (PHP)** -- API ile etkileşim kuran kullanıcı dostu web arayüzü.

### Canlı Örnek Web Uygulaması
 **https://datasetgenerator.42web.io** (Alan adı güncellenecek)
 
---
## Özellikler

### Desteklenen Çıktı Formatları
| Format |
|--------|
| JSON |
| JSONL |
| CSV |
| TSV | 
| SQL | 
| XML |
| YAML |
### Veri Kaynakları
Projede 3 adet veri kaynağı bulunmaktadır. Bu veri kaynakları, oluşturulması istenen veri setini en verimli şekilde oluşturma amacıyla belirlenmiştir.
- **Default (Varsayılan):** Varsayılan veriler, genel olarak veri setlerinde sıkça kullanılan veya özelleştirilmiş veri setleri baz alınarak tasarlanmış verilerden oluşur.

| Başlık | Açıklama |
|------------------------|---------------------------------------|
| `default-nums-asc` | Artan sayılar: 1, 2, ..., N |
| `default-nums-desc` | Azalan sayılar: N, N-1, ..., 1 |
| `default-nums-rand` | Rastgele tamsayı [1, N] |
| `default-az` | a, b, ..., z (döngüsel) |
| `default-za` | z, y, ..., a (döngüsel) |
| `default-AZ` | A, B, ..., Z (döngüsel) |
| `default-ZA` | Z, Y, ..., A (döngüsel) |
| `default-aZ` | a-z ardından A-Z (döngüsel) |
| `default-Az` | A-Z ardından a-z (döngüsel) |
| `default-zA` | z-a ardından Z-A (döngüsel) |
| `default-Za` | Z-A ardından z-a (döngüsel) |
| `default-az-rand` | Rastgele küçük harf |
| `default-AZ-rand` | Rastgele büyük harf |
| `default-Az-rand` | Rastgele harf (karışık, büyük/küçük) |
| `default-access-log` | flog ile oluşturulmuş rastgele access log entry'leri |
Gün geçtikçe varsayılan verilerin artması beklenmektedir, kullanıcı api/default.py dosyasına gerekli Python fonksiyonlarıyla kendi varsayılan verilerini ekleyebilir, tercihen orijinal repo'ya pull request gönderebilir.

-  **File (Dosya):** Düz metin dosyası yüklenebilir, her satır bir değeri temsil eder. Dosya yüklerken ek seçenekler kullanılabilir:
-- Cycle: Satırları döngüsel olarak tekrarlar ve tüm satırları doldurur.
-- Random: Dosyanın içinde değer olarak kullanılan satırları karıştırır.
Bu iki özellik birlikte de kullanılabilir.

-  **Custom (Özel):** Her satır/satır aralığı için özel string veriler tanımlanabilir
---
## Proje Yapısı
```
DatasetGenerator/
├── api
│   ├── app.py
│   ├── defaults.py
│   ├── docker-compose.yml
│   ├── Dockerfile
│   ├── generator.py
│   └── requirements.txt
├── app
│   ├── docker-compose.yml
│   ├── Dockerfile
│   └── index.php
├── LICENSE
└── README.md
```
---  
## Kurulum
### Gereksinimler
  - Docker
  
veya

&nbsp;&nbsp;&nbsp;&nbsp; API için
- Python 3.x
- Python Pip
- `go` (flog kurulumu için)
-  `flog` (access logu üretmek için, yalnızca `default-access-log` için gereklidir. Flog kurulumu için: https://github.com/mingrammer/flog)
-  `libmagic` (dosya tipini doğrulamak için)

&nbsp;&nbsp;&nbsp;&nbsp;Web uygulaması için
- PHP (tercihen 8.x)

.env dosyalarının içerikleri istenen konfigürasyonlara göre değiştirilmelidir.
### Docker ile Kurulum ve Çalıştırma
#### API Kurulumu
```bash
git  clone  https://github.com/muhammedikbalyildiz/DatasetGenerator.git
cd  DatasetGenerator/api
sudo docker compose up --build
```
#### Web Uyulaması Kurulumu
```bash
git  clone  https://github.com/muhammedikbalyildiz/DatasetGenerator.git
cd  DatasetGenerator/app
sudo docker compose up --build
```
<br></br>
### Alternatif Kurulum ve Çalıştırma (Docker Olmadan)
#### API Kurulumu
```bash
git  clone  https://github.com/muhammedikbalyildiz/DatasetGenerator.git
cd  DatasetGenerator/api
pip install -r requirements.txt
python3 app.py
```
#### Web Uygulaması Kurulumu
```bash
git  clone  https://github.com/muhammedikbalyildiz/DatasetGenerator.git
cd  DatasetGenerator/app
php -S 0.0.0.0:8080
```
## Katkı
Bu proje her türlü katkıya açıktır. Özellikle default verilerin artırılması, custom verilerin özelleştirilmesi ve yeni veri yöntemlerinin eklenmesi başta olmak üzere tüm katkılar özenle değerlendirmeye alınacaktar.

---
---
<a  id="english"></a>

## [EN] 
## Description
Dataset Generator is an open-source tool that helps generating datasets quickly and easily for researchers, developers, and data engineers. The project consists of two main components:
-  **API (Flask)** -- A REST API service that performs dataset generation on the server.

-  **Web Application (PHP)** -- A user-friendly web interface that interacts with the API.

### Sample Live Web Application
 **https://datasetgenerator.42web.io** (Domain name will be updated)
 
---
## Features

### Supported Output Formats
| Format |
|--------|
| JSON |
| JSONL |
| CSV |
| TSV | 
| SQL | 
| XML |
| YAML |
### Data Sources
There are 3 data sources in the project. These data sources have been selected to generate the desired dataset in the most efficient manner.
- **Default:** Default values are generally composed of values that are frequently used in datasets or designed based on customized datasets.

| Header | Description |
|------------------------|---------------------------------------|
| `default-nums-asc` | Ascending numbers: 1, 2, ..., N |
| `default-nums-desc` | Descending numbers: N, N-1, ..., 1 |
| `default-nums-rand` | Random integer [1, N] |
| `default-az` | a, b, ..., z (cycled) |
| `default-za` | z, y, ..., a (cycled) |
| `default-AZ` | A, B, ..., Z (cycled) |
| `default-ZA` | Z, Y, ..., A (cycled) |
| `default-ZA` | Z, Y, ..., A (cycled) |
| `default-aZ` | a-z followed by A-Z (cycled) |
| `default-zA` | z-a followed by Z-A (cycled) |
| `default-Za` | Z-A followed by z-a (cycled) |
| `default-az-rand` | Random lowercase letters |
| `default-AZ-rand` | Random uppercase letters |
| `default-Az-rand` | Random letters (mixed, uppercase/lowercase) |
| `default-access-log` | Random access log entries generated by flog |
As time goes on, the number of default values is expected to increase. Users can add their own default values to the api/default.py file using the necessary Python functions, and are encouraged to submit a pull request to the original repository.
-  **File:** A plain text file can be uploaded, each line represents a value. Additional options can be used when uploading the file:
-- Cycle: Repeats the lines cyclically and fills all the rows.
-- Random: Shuffles the lines used as values within the file.
These two features can also be combined.

-  **Custom (Custom):** Custom string data can be defined for each line/line range
---
## Project Structure
```
DatasetGenerator/
├── api
│   ├── app.py
│   ├── defaults.py
│   ├── docker-compose.yml
│   ├── Dockerfile
│   ├── generator.py
│   └── requirements.txt
├── app
│   ├── docker-compose.yml
│   ├── Dockerfile
│   └── index.php
├── LICENSE
└── README.md
```
---  
## Installation
### Requirements
  - Docker
  
or

&nbsp;&nbsp;&nbsp;&nbsp; For API
- Python 3.x
- Python Pip
- `go` (for flog installation)
-  `flog` (to generate access logs; required only for `default-access-log`. For flog installation: https://github.com/mingrammer/flog)
- `libmagic` (to verify file types)

&nbsp;&nbsp;&nbsp;&nbsp;For web application
- PHP (preferably 8.x)

The contents of the .env files must be modified based on desired configurations.

### Setup and Running with Docker
#### API Setup
```bash
git clone https://github.com/muhammedikbalyildiz/DatasetGenerator.git
cd DatasetGenerator/api
sudo docker-compose up --build
```
#### Web Application Setup
```bash
git clone https://github.com/muhammedikbalyildiz/DatasetGenerator.git
cd DatasetGenerator/app
sudo docker-compose up --build
```

<br></br>

### Alternative Installation and Running (Without Docker)
#### API Setup
```bash
git clone https://github.com/muhammedikbalyildiz/DatasetGenerator.git
cd DatasetGenerator/api
pip install -r requirements.txt
python3 app.py
```
#### Web Application Setup
```bash
git clone https://github.com/muhammedikbalyildiz/DatasetGenerator.git
cd DatasetGenerator/app
php -S 0.0.0.0:8080
```
## Contributions
This project is open to all kinds of contributions. All contributions—primarily those related to expanding the default values, customizing custom data, and adding new data methods—will be carefully reviewed.
