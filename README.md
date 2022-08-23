## Скрипт протестирован в PHP 8.1.4 (cli). Дополнительные библиотеки не трубеются.

Файл **ThomasFullFeature.php** реализует класс для **сэмплированания по Томпсону с бета-распределением**. За основу взята математическая модель, описанная на https://3-info.ru/post/44437?page=33

На входе массив статистических данных ввиде:

```php
[ 'заголовок' => ['кол-во просмотров', 'кол-во кликов'] ]
```

**Например:** Запись **"Hello world"** имеет 10 просмотров и 0 клика. Тогда собирается массив:

```php
[
"Hello world"=>[10, 0]
]
```

**Код:**

```php
$th = new ThompsonSempl(['a1'=>[10, 0],'a2'=>[32, 4], 'a3'=>[321,80]]);

$th->predict(true);

// return
//      Array
//      (
//          [a3] => 0.2547
//          [a1] => 0.1755
//          [a2] => 0.1477
//      )
```

Метод `predict(): array` выполняет расчет приоритетов каждой записи на основе бета-распределния и возвращает результирующий массив.

**Чем больше** значение для класса, тем выше приоритет. В данном примере преоритет в порядке `a3->a1->a2`.

### Мат. логика (кратко)

Данный способ реализации сэмплирования значительно производительнее предыдущего, поскольку критерием отбора является расчет параметров **математического ожидания**
$$μ=E(X) = alpha/(alpha+beta)$$
**и дисперсии**
$$D = alpha * beta/((alpha+beta)^2*(alpha*beta+1))$$
где 
$$alpha, betta ∈ [1,n], n=R^+$$

### Тестирование

Для тестрования случайным образом выбираются характеристики просмотров и кликов, а также количество записей `100К, 300К, 600К, 1M.`

```php
for 100K: 0.1958 сек. / 39.87 МБ
for 300K: 0.5983 сек. / 123.59 МБ
for 600K: 1.1853 сек. / 247.16 МБ
for 1M: 1.95 сек. / 390.6 МБ
```
