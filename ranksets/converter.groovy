if(args.length != 1) {
    println "groovy converter <filename>"
    System.exit(1)
}

class Rank {
    public int num
    public String image
    public String text
    
    Rank(int num, String image, String text) {
        this.num = num
        this.image = image
        this.text = text
    }
}

File f = new File(args[0])

def ranks = []

f.eachLine { line ->
    if((line = line.trim())) {
        //array("num" => 0, "image" => '', "text" => 'Non-poster'),
        def reg = /array\("(.*)" => ([0-9]+), "(.*)" => '(.*)', "(.*)" => '(.*)'\)(\,)?/
        if(line ==~ reg) {
            def group = (line =~ reg)
            def num = group[0][2].toInteger()
            def image = group[0][4]
            def text = group[0][6]
            println "Found Rank: " + num + " : " + image + " : " + text
            ranks << new Rank(num, image, text)
        }
    }
}

StringBuilder builder = new StringBuilder()
builder.append("{\n")
for(rank in ranks) {
    builder.append("\t\"${rank.text}\": {\n\t\t\"num\": ${rank.num},\n\t\t\"image\": \"${rank.image}\"\n\t}${ranks.last() == rank ? "" : ","}\n")
}
builder.append("}\n")
new File(f.getPath()[0..-5] + ".json").write builder.toString()
