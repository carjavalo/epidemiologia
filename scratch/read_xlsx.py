import zipfile
import xml.etree.ElementTree as ET
import re
import os

file_path = r'C:\Users\Soporte\Desktop\Proyectos HUV\proahuv\proahuv\docu\SEGUIMIENTO MICROBIOLÓGICO (1).xlsx'

if not os.path.exists(file_path):
    print(f"File not found: {file_path}")
    exit(1)

try:
    with zipfile.ZipFile(file_path, 'r') as z:
        shared_strings = []
        if 'xl/sharedStrings.xml' in z.namelist():
            ss_data = z.read('xl/sharedStrings.xml')
            root = ET.fromstring(ss_data)
            ns = {'ns': 'http://schemas.openxmlformats.org/spreadsheetml/2006/main'}
            for si in root.findall('ns:si', ns):
                t = si.find('ns:t', ns)
                if t is not None:
                    shared_strings.append(t.text or '')
                else:
                    text_parts = []
                    for r in si.findall('ns:r', ns):
                        rt = r.find('ns:t', ns)
                        if rt is not None and rt.text:
                            text_parts.append(rt.text)
                    shared_strings.append(''.join(text_parts))

        if 'xl/worksheets/sheet1.xml' in z.namelist():
            sheet_data = z.read('xl/worksheets/sheet1.xml')
            root = ET.fromstring(sheet_data)
            ns = {'ns': 'http://schemas.openxmlformats.org/spreadsheetml/2006/main'}
            
            def get_value(c_node):
                t_attr = c_node.get('t')
                v_node = c_node.find('ns:v', ns)
                if v_node is None:
                    return None
                val = v_node.text
                if t_attr == 's' and val is not None:
                    idx = int(val)
                    if 0 <= idx < len(shared_strings):
                        return shared_strings[idx]
                return val

            print("Searching for non-empty rows...")
            found = 0
            for row_node in root.findall('.//ns:row', ns):
                row_idx = int(row_node.get('r'))
                row_cells = []
                for c_node in row_node.findall('ns:c', ns):
                    val = get_value(c_node)
                    if val is not None and str(val).strip() != '':
                        cell_ref = c_node.get('r')
                        col = re.match(r'^([A-Z]+)', cell_ref).group(1)
                        row_cells.append(f"{col}: {val}")
                if len(row_cells) > 3:
                    print(f"Row {row_idx} ({len(row_cells)} cells): " + " | ".join(row_cells[:8]))
                    found += 1
                    if found > 15:
                        break
                    
except Exception as e:
    print(f"Error: {e}")
