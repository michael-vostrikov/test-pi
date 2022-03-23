# Parallel processes in PHP

Test task for calculation on PI number by Monte-Carlo method using parrallel processes in PHP.
Implemented with OOP and interfaces, documented with PHPDoc. Used Factory and Strategy patterns.

- Processes are run with proc_open() function.\
- Process management is done with a process manager.\
- Manager can send messages to child processes at any time. Processes can respond to messages at any time. One condition - it's needed to call message check manually from iterations of calculation process.\
- Message exchange goes through file descriptors which are used instead of STDIN, STDOUT, STDERR.\
- Works on all operating systems (Linux and Windows).\
- With classes from ParallelLibrary it is possible to implement any message protocol.\

Examples are in files PiCalculationWorkerManager / PiCalculationProcess and PingPongWorkerManager / PingPongProcess.


# Параллельные процессы в PHP

Тестовое задание по вычислению числа PI методом Монте-Карло с использованием параллельных процессов в PHP.  
Реализовано с использованием ООП и интерфейсов, документировано в стиле PHPDoc. Применены паттерны проектирования Фабрика и Стратегия.

- Процессы запускаются с помощью функции proc_open().  
- Управление процессами осуществляется специальным менеджером.  
- Менеджер может посылать сообщения дочерним процессам в любой момент времени. Процессы могут отвечать на сообщения в любой момент времени. Единственное условие - проверку наличия сообщений надо вызывать в итерациях вычислений вручную.  
- Обмен сообщениями идет через файловые дескрипторы, которые заменяют потоки STDIN, STDOUT, STDERR.  
- Работает на всех операционных системах (Linux и Windows).  
- На основе классов из папки ParallelLibrary можно реализовать любой протокол передачи сообщений.  

Примеры в файлах PiCalculationWorkerManager / PiCalculationProcess и PingPongWorkerManager / PingPongProcess.
