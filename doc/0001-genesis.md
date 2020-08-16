# Genesis block

### pszTimestamp

A proof, a name and a salt.

**Examples:**

Bitcoin:
```$xslt
The Times 03/Jan/2009 Chancellor on brink of second bailout for banks
``` 
Zcash:
```$xslt
The Economist 2016-10-29 Known unknown: Another crypto-currency is born. BTC#436254 0000000000000000044f321997f336d2908cf8c8d6893e88dbf067e2d949487d ETH#2521903 483039a6b6bd8bd05f0584f9a078d075e454925eb71c1f13eaff59b405a721bb DJIA close on 27 Oct 2016: 18,169.68
```

### Bits

The highest possible target (difficulty 1) is defined as 0x1d00ffff, which gives us a hex target of:
```$xslt
0x00ffff * 2**(8*(0x1d - 3)) = 0x00000000FFFF0000000000000000000000000000000000000000000000000000
```

It should be noted that pooled mining often uses non-truncated targets, which puts "pool difficulty 1" at:
```$xslt
0x00000000FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF
```

So the difficulty at 0x1b0404cb is therefore:
```$xslt
0x00000000FFFF0000000000000000000000000000000000000000000000000000 /
0x00000000000404CB000000000000000000000000000000000000000000000000 
= 16307.420938523983 (bdiff)
```

And:

```$xslt
0x00000000FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF /
0x00000000000404CB000000000000000000000000000000000000000000000000 
= 16307.669773817162 (pdiff)
```

Here's a fast way to calculate bitcoin difficulty. It uses a modified Taylor series for the logarithm (you can see tutorials on flipcode and wikipedia) and relies on logs to transform the difficulty calculation:

```c
#include <iostream>
#include <cmath>

inline float fast_log(float val)
{
   int * const exp_ptr = reinterpret_cast <int *>(&val);
   int x = *exp_ptr;
   const int log_2 = ((x >> 23) & 255) - 128;
   x &= ~(255 << 23);
   x += 127 << 23;
   *exp_ptr = x;

   val = ((-1.0f/3) * val + 2) * val - 2.0f/3;
   return ((val + log_2) * 0.69314718f);
} 

float difficulty(unsigned int bits)
{
    static double max_body = fast_log(0x00ffff), scaland = fast_log(256);
    return exp(max_body - fast_log(bits & 0x00ffffff) + scaland * (0x1d - ((bits & 0xff000000) >> 24)));
}

int main()
{
    std::cout << difficulty(0x1b0404cb) << std::endl;
    return 0;
}
```

To see the math to go from the normal difficulty calculations (which require large big ints bigger than the space in any normal integer) to the calculation above, here's some python:

```$xslt
import decimal, math
l = math.log
e = math.e

print 0x00ffff * 2**(8*(0x1d - 3)) / float(0x0404cb * 2**(8*(0x1b - 3)))
print l(0x00ffff * 2**(8*(0x1d - 3)) / float(0x0404cb * 2**(8*(0x1b - 3))))
print l(0x00ffff * 2**(8*(0x1d - 3))) - l(0x0404cb * 2**(8*(0x1b - 3)))
print l(0x00ffff) + l(2**(8*(0x1d - 3))) - l(0x0404cb) - l(2**(8*(0x1b - 3)))
print l(0x00ffff) + (8*(0x1d - 3))*l(2) - l(0x0404cb) - (8*(0x1b - 3))*l(2)
print l(0x00ffff / float(0x0404cb)) + (8*(0x1d - 3))*l(2) - (8*(0x1b - 3))*l(2)
print l(0x00ffff / float(0x0404cb)) + (0x1d - 0x1b)*l(2**8)
```


