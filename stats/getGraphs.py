# coding: utf8

import sys
import os
script_dir = os.path.dirname(os.path.realpath(__file__))
os.chdir(script_dir)
sys.path.append('ext_libs')
import matplotlib
matplotlib.use("Agg")
import matplotlib.pyplot as plt
import numpy as np
import toolforge

conn = toolforge.toolsdb("s53950__SWViewer")

def get_values(query):
    with conn.cursor() as cur:
        cur.execute(query)
        result = cur.fetchall()
    return result

values = get_values("SELECT count(*) as cnt, date(date) as timestamp FROM logs WHERE date(date) >= date(now()) - INTERVAL 10 day AND date(date) <> CURDATE() GROUP BY date(date) ORDER BY timestamp ASC;")
val1 = []
val2 = [];
for el in values:
    val1.append(el[0])
    val2.append(el[1].strftime("%d-%m-%Y"))

fig_size = plt.rcParams["figure.figsize"]
fig_size[0] = 8
fig_size[1] = 8
plt.rcParams["figure.figsize"] = fig_size
plt.xlabel("Days")
plt.ylabel("Actions")
plt.grid()
plt.title("Actions per day (last 10 days)")
xn = range(len(val2))
plt.plot(xn, val1, "r--")
plt.xticks(xn, val2)
plt.gcf().autofmt_xdate()
plt.savefig("actions-count-10.png")
plt.close()


values = get_values("SELECT count(*) as cnt, type FROM logs WHERE date(date) >= date(now()) - INTERVAL 10 day AND date(date) <> CURDATE() GROUP BY type;")
val1 = []
val2 = [];
for el in values:
    val1.append(el[0])
    val2.append(el[1])

def getIndexType(t, arr1, arr2):
    try:
        t_raw = arr2.index(t)
    except ValueError:
        t_raw = -1
    if t_raw == -1:
        return 0
    else:
        return arr1[t_raw]

plt.title("Actions by type (last 10 days)")
val_type = [getIndexType('rollback', val1, val2), getIndexType('undo', val1, val2), getIndexType('delete', val1, val2), getIndexType('edit', val1, val2), getIndexType('warn', val1, val2), getIndexType('report', val1, val2), getIndexType('protect', val1, val2)]
y = np.array(val_type)

xn = range(len(["Rollbacks", "Undo", "Delete", "Edits", "Warns", "Reports", "Protects"]))
plt.bar(xn, y, color=["#c8b40e", "#db24b0", "#672dd2", "#2dd280", "#d92c26", "#e3791c", "#1cb3e3"])
plt.xticks(xn, ["Rollbacks", "Undo", "Delete", "Edits", "Warns", "Reports", "Protects"])
for index, value in enumerate(val_type):
    plt.text(index, value, str(value), ha="center")
plt.savefig("actions-type-10.png")
plt.close()




values = get_values("SELECT count(*) as cnt, user FROM logs WHERE date(date) >= date(now()) - INTERVAL 10 day AND date(date) <> CURDATE() GROUP BY user ORDER BY cnt DESC LIMIT 10;")
val1 = []
val2 = [];
for el in values:
    val1.append(el[0])
    val2.append(el[1])


plt.title("Actions by users (last 10 days)")
y = np.array(val1)
xn = range(len(val2))
plt.bar(xn, y, color=["#c8b40e", "#db24b0", "#672dd2", "#2dd280", "#d92c26"])
plt.xticks(xn, val2, rotation=45)
for index, value in enumerate(val1):
    plt.text(index, value, str(value), ha="center")
plt.savefig("actions-users-10.png")
plt.close()


values = get_values("SELECT count(*) as cnt, wiki FROM logs WHERE date(date) >= date(now()) - INTERVAL 10 day AND date(date) <> CURDATE() GROUP BY wiki ORDER BY cnt DESC;")
val1 = []
val2 = [];
i = 0
total = 0
for el in values:
    i = i + 1
    if i <= 9:
        val1.append(el[0])
        val2.append(el[1])
    else:
        total = total + el[0]

val1.append(total)
val2.append("Others")

plt.title("Actions by wikis (last 10 days)")
y = np.array(val1)
xn = range(len(val2))
plt.bar(xn, y, color=["#c8b40e", "#db24b0", "#672dd2", "#2dd280", "#d92c26"])
plt.xticks(xn, val2, rotation=45)
for index, value in enumerate(val1):
    plt.text(index, value, str(value), ha="center")
plt.savefig("actions-wikis-10.png")
plt.close()


