# BahtReader
### Thai number-to-word conversion library in PHP.

To get started, just instantiate a new object and call `read()` method.

For example:
```php
    $reader = new BahtReader;
    echo $reader->read('1,234,567.89'); // will output หนึ่งล้านสองแสนสามหมื่นสี่พันห้าร้อยหกสิบเจ็ดบาทแปดสิบเก้าสตางค์
```

This library contains 3 functions:
- `money_validate($input, $delimiter = ',', $separator = '.')` removes all non-numeric characters from the input string, with the exception of the separator.
- `money_format($input, $delimiter = ',', $separator = '.')` properly formats input (aka put in all the delimiters and separator at the right position)
- `read($input, $currency = 'บาท', $sub_currency = 'สตางค์', $separator = '.')` converts the numeric currency string, formatted or not, to words. Automatically adds currency (such as บาท and สตางค์) to the output `$currency` and `$sub_currency` is replaced by empty strings.
- `spell($input)` converts the numeric string to words. Will fail if non-numeric characters are present.

Contributions to features and languages are welcome.
