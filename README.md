# Project setup

## Choose your runner:  
Docker:  
`$ runner="docker run --rm -u 1000:1000 -t -v $PWD:/app -w /app composer:2.8"` 

Local PHP installation:  
`$ runner=composer`

## Install dependencies

`$ $runner install`

# Unit tests

Run:  
`$ $runner unit`

# Additional information

1. I had already implemented Payment and Fee amounts separately as this was my solution of first choice, when got answer from Tomasz that this is not especially required in this task. Didn't have time to refactor this. However, the main goal, i.e. balance has to be correct, is achieved.
2. Unit tested as much as possible in rational time spent on the task. Started testing from most critical classes to less critical.
3. I also left some inline comments on the code. Such comments start with `// comment:`. You can read them as you go or search for all of them using global search for `// comment:`  in your IDE.
4. If you have any questions why I've implemented something this way rather than another, I will be happy to answer during the next interview stage (if happens). 