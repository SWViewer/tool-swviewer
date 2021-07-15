import json
from urllib.request import urlopen

"""
Wiki list updater
https://quarry.wmflabs.org/query/33179
Submit query (need to sign in) => Download data => download as json file.
Rename file to list.json and put in the script's dir.

https://meta.wikimedia.org/wiki/Small_Wiki_Monitoring_Team/Groups
Replace: space => \n, $ => ".org" (regex).
"official.txt" put in the script's dir.

After finished: transform to arrays (["a", "b", "c"]) and upload to swviewer-service => service => storage.js
Update https://meta.wikimedia.org/wiki/SWViewer/wikis
"""

print("Starting...")

print("Get lists...")
with open("list.json") as jsonFile:
    all_wikis_raw = json.load(jsonFile)
    jsonFile.close()

with open("official.txt") as txtFile:
    official_wikis_raw = txtFile.readlines()
    official_wikis_raw = [x.rstrip('\n') for x in official_wikis_raw]
    txtFile.close()

closed_wikis = urlopen("https://noc.wikimedia.org/conf/dblists/closed.dblist").readlines()
closed_wikis = [x.decode('UTF-8').rstrip('\n') for x in closed_wikis[1:]]
deleted_wikis = urlopen("https://noc.wikimedia.org/conf/dblists/deleted.dblist").readlines()
deleted_wikis = [x.decode('UTF-8').rstrip('\n') for x in deleted_wikis[1:]]
private_wikis = urlopen("https://noc.wikimedia.org/conf/dblists/private.dblist").readlines()
private_wikis = [x.decode('UTF-8').rstrip('\n') for x in private_wikis[1:]]
test_wikis = urlopen("https://noc.wikimedia.org/conf/dblists/testwikis.dblist").readlines()
test_wikis = [x.decode('UTF-8').rstrip('\n') for x in test_wikis[1:]]
fishbowl_wikis = urlopen("https://noc.wikimedia.org/conf/dblists/fishbowl.dblist").readlines()
fishbowl_wikis = [x.decode('UTF-8').rstrip('\n') for x in fishbowl_wikis[1:]]

additional_wikis = ["apiportalwiki", "testcommonswiki", "loginwiki", "test2wiki"]

all_wikis = []
for wiki in all_wikis_raw["rows"]:
    if wiki[0] not in closed_wikis and wiki[0] not in deleted_wikis \
            and wiki[0] not in private_wikis and wiki[0] not in test_wikis \
            and wiki[0] not in fishbowl_wikis and wiki[0] not in additional_wikis:
        all_wikis.append(wiki)

print("Total wikis: " + str(len(all_wikis)))

official_wikis = []
for off in official_wikis_raw:
    c = False
    if off == "mediawiki.wikipedia.org":
        off = "www.mediawiki.org"
    if off == "outreach.wikipedia.org":
        off = "outreach.wikimedia.org"
    for i in all_wikis:
        if i[2] == "https://" + off:
            c = True
    if not c:
        print("Stranger official wiki: " + off)  # check if "official list" contains something stranger
    else:
        official_wikis.append(off)

print("Starting get count of articles...")
small_wikis = []
ls300 = []
i = 0
for wiki in all_wikis:
    i = i + 1
    print(str(len(all_wikis) - i) + ": ..." + wiki[2] + "...")
    req = json.load(
        urlopen(wiki[2] + "/w/api.php" + "?action=query&format=json&meta=siteinfo&siprop=statistics&utf8=1"))
    if req["query"]["statistics"]["articles"] < 10000:
        small_wikis.append(wiki)
    if req["query"]["statistics"]["articles"] >= 10000 and req["query"]["statistics"]["activeusers"] < 300:
        ls300.append(wiki)

print("Merge with official list")
i = 0
for off in official_wikis:
    i = i + 1
    c = False
    for small in small_wikis:
        if small[2] == "https://" + off:
            c = True
    if not c:
        req = json.load(urlopen("https://" + off + "/w/api.php" + "?action=query&format=json&meta=siteinfo&siprop"
                                                                  "=general%7Cstatistics&utf8=1"))
        print(str(len(official_wikis) - i) + ": Add to small wikis list from official list: " + off + ". Articles: " +
              str(req["query"]["statistics"]["articles"]))
        small_wikis.append([req["query"]["general"]["wikiid"], "", "https://" + off])
    else:
        print(str(len(official_wikis) - i) + ": Skipping.")

print("Cleaning ls300 list..")
ls300_raw = ls300
ls300 = []
for wiki in ls300_raw:
    c = False
    for small in small_wikis:
        if small[0] == wiki[0]:
            c = True
    if not c:
        ls300.append(wiki)

print("Writing files...")
with open('small_wikis.txt', 'w') as smallFile:
    for wiki in small_wikis:
        smallFile.write(wiki[0] + '\n')
    smallFile.close()
with open('ls300_wikis.txt', 'w') as lsFile:
    for wiki in ls300:
        lsFile.write(wiki[0] + '\n')
    lsFile.close()

print("Small wikis list items: " + str(len(small_wikis)))
print("Ls300 wikis list items: " + str(len(ls300)))
print("Total: " + str(len(small_wikis) + len(ls300)))
print("End of working.")
