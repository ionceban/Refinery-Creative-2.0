#include <iostream>
#include <sstream>
#include <fstream>
#include <cstdlib>
#include <cstring>
#include <set>
#include <vector>
#include <string>
#define MAXN 10000
#define MAXTABLES 10000
using namespace std;
int N, NBigCells;
int ImageTypes[MAXN], ImageSizes[MAXN];
set<int> WhiteSpaces;
string Response;

struct table{
	int image_source;
	int THeight, TWidth;
	int NRows, NCols;
	bool full;
	table* Content[5][5];
} *Tables[MAXTABLES];


void table_init(table* T, int THeight, int TWidth){
	T->NRows = 0;
	T->NCols = 0;
	T->THeight = THeight;
	T->TWidth = TWidth;
	T->full = false;
	T->image_source = 0;

	for (int i = 0; i < 5; ++i)
		for (int j = 0; j < 5; ++j)
			T->Content[i][j] = NULL;
}

int insert_into(table* T, int img_height, int img_width, int img_src){
	if (T->NRows == 0 && T->NCols == 0){
		
		if (T->THeight == img_height && T->TWidth == img_width){
			T->image_source = img_src;
			T->full = true;
			return 1;
		} else {
			
			if (T->THeight == 2){
				if (T->TWidth == 2){

					T->NRows = 2;
					T->NCols = 1;

					T->Content[1][1] = new table;
					T->Content[2][1] = new table;

					table_init(T->Content[1][1], 1, 2);
					table_init(T->Content[2][1], 1, 2);

					return insert_into(T->Content[1][1], img_height, img_width, img_src);
				} else {
					
						T->NRows = 1;
						T->NCols = 2;

						T->Content[1][1] = new table;
						T->Content[1][2] = new table;

						table_init(T->Content[1][1], 2, 2);
						table_init(T->Content[1][2], 2, 2);

						return insert_into(T->Content[1][1], img_height, img_width, img_src);
				}
			} else {

				T->NRows = 1;
				T->NCols = 2;
				
				T->Content[1][1] = new table;
				T->Content[1][2] = new table;

				table_init(T->Content[1][1], 1, 1);
				table_init(T->Content[1][2], 1, 1);

				return insert_into(T->Content[1][1], img_height, img_width, img_src);
			}
		}

	} else {
		int result = 0;
		
		for (int i = 1; i <= T->NRows; ++i)
			for (int j = 1; j <= T->NCols; ++j){
				table* PossibleTable = T->Content[i][j];
				if (PossibleTable->image_source == 0 && PossibleTable->THeight >= img_height && PossibleTable->TWidth >= img_width)
					if (insert_into(PossibleTable, img_height, img_width, img_src)){
						result = 1;
						i = T->NRows + 1;
						j = T->NCols + 1;
					}
			}

		bool is_full = true;
		
		for (int i = 1; i <= T->NRows; ++i)
			for (int j = 1; j <= T->NCols; ++j){
				table* ChildTable = T->Content[i][j];
				if (ChildTable->full == false){
					is_full = false;
					i = T->NRows + 1;
					j = T->NCols + 1;
				}
			}

		T->full = is_full;
		return result;
	}
}

int fit_image(int img_src, int img_type, int img_size){

	int image_height = 0, image_width = 0;

	if (img_type == 0){
		if (img_size == 0){
			image_height = 1;
			image_width = 1;
		} else {
			image_height = 2;
			image_width = 2;
		}
	} else {
		if (img_size == 0){
			image_height = 1;
			image_width =2;
		} else {
			image_height = 2;
			image_width = 4;
		}
	}

	for (set<int>::iterator it = WhiteSpaces.begin(); it != WhiteSpaces.end(); ++it){
		int TableIndex = *it;
		if (Tables[TableIndex]->full == false && insert_into(Tables[TableIndex], image_height, image_width, img_src)){
			if (Tables[TableIndex]->full == true) WhiteSpaces.erase(it);
			return TableIndex;
		}
	}

	Tables[++NBigCells] = new table;
	table_init(Tables[NBigCells], 2, 4);
	WhiteSpaces.insert(NBigCells);
	
	Tables[++NBigCells] = new table;
	table_init(Tables[NBigCells], 2, 4);
	WhiteSpaces.insert(NBigCells);

	if (insert_into(Tables[NBigCells-1], image_height, image_width, img_src)){
		if (Tables[NBigCells-1]->full == true) WhiteSpaces.erase(WhiteSpaces.find(NBigCells-1));
		return NBigCells-1;
	}
}

void build_Response(table* T){
	Response += "<table border='0' cellspacing='0' cellpadding='0'>";
	if (T->THeight == 2){
		if (T->TWidth == 4){
			if (T->image_source != 0){
				Response += "<tr><td><div style='width:468px; height:318px'><a><img src='dummy_source";
				
				stringstream dummy_ss;
				dummy_ss << T->image_source;
				Response += dummy_ss.str();

				Response += "' /></a></div></td></tr>";
			} else if (T->NRows > 0 && T->NCols > 0){
				Response += "<tr><td><div style='width:234px; height:318px'><div style='width:222px; height:318px; margin-right:12px'>";

				build_Response(T->Content[1][1]);

				Response += "</div></div></td><td><div style='width:234px; height:318px'><div style='width:222px; height:318px; margin-left:12px'>";

				build_Response(T->Content[1][2]);

				Response += "</div></div></td></tr>";
			} else {
				Response += "<tr><td><div style='width:468px; height:318px'></div></td></tr>";
			}
				
		} else {
			if (T->image_source != 0){
				Response += "<tr><td><div style='width:222px; height:318px'><a><img src='dummy_source";

				stringstream dummy_ss;
				dummy_ss << T->image_source;
				Response += dummy_ss.str();

				Response += "' /></a></div></td></tr>";
			} else if (T->NRows > 0 && T->NCols > 0){
				Response += "<tr><td><div style='width:222px; height:159px'><div style='width:222px; height: 147px; margin-bottom:12px'>";
				
				build_Response(T->Content[1][1]);

				Response += "</div></div></td></tr><tr><td><div style='width:222px; height:159px'><div style='width:222px; height:147px; margin-top:12px'>";

				build_Response(T->Content[2][1]);

				Response += "</div></div></td></tr>";

			} else {
				Response += "<tr><td><div style='width:222px; height:318px'></div></td></tr>";
			} 
		}
	} else {
		if (T->TWidth == 2){
			if (T->image_source != 0){
				Response += "<tr><td><div style='width:222px; height:147px'><a><img src='dummy_source";

				stringstream dummy_ss;
				dummy_ss << T->image_source;
				Response += dummy_ss.str();

				Response += "' /></a></div></td></tr>";
			} else if (T->NRows > 0 && T->NCols > 0){
				Response += "<tr><td><div style='width:111px; height:147px'><div style='width:99px; height:147px; margin-right:12px'>";

				build_Response(T->Content[1][1]);

				Response += "</div></div></td><td><div style='width:111px; height:147px'><div style='width:99px; height:147px; margin-left:12px'>";

				build_Response(T->Content[1][2]);

				Response += "</div></div></td></tr>";
			} else {
				Response += "<tr><td><div style='width:222px; height:147px'></div></td></tr>";
			}
		} else {
			if (T->image_source != 0){
				Response += "<tr><td><div style='width:99px; height:147px'><a><img src='dummy_source";

				stringstream dummy_ss;
				dummy_ss << T->image_source;
				Response += dummy_ss.str();

				Response += "' /></a></div></td></tr>";
			} else {
				Response += "<tr><td><div style='width:99px; height:147px'></div></td></tr>";
			}
		}
	}
	Response += "</table>";
}

int main(int argc, char* argv[]){
	
	N = 0;
	for (unsigned int i = 0; i < strlen(argv[1]); ++i){
		N *= 10;
		N += argv[1][i] - '0';
	}

	for (int i = 0; i < N; ++i)
		ImageTypes[i+1] = argv[2][i] - '0';

	for (int i = 0; i < N; ++i)
		ImageSizes[i+1] = argv[3][i] - '0';

	NBigCells = 0;

	for (int i = 1; i <= N; ++i)
		fit_image(i, ImageTypes[i], ImageSizes[i]);

	Response = "";
	
	Response += "<table border='0' cellspacing='0' cellpadding='0'>";
	
	for (int i = 1; i <= NBigCells; ++i)
		if (i % 2){
			Response += "<tr><td>";
			if (i == 1){
				Response += "<div style='width:480px; height:330px'><div style='width:468px; height:318px; margin-right: 12px; margin-bottom:12px'>";
			} else {
				Response += "<div style='width:480px; height:342px'><div style='width:468px; height:318px; margin-right: 12px; margin-bottom:12px; margin-top:12px'>";
			}

			build_Response(Tables[i]);

			Response += "</div></div></td>";
		} else {
			Response += "<td>";
			if (i == 2){
				Response += "<div style='width:480px; height:330px'><div style='width:468px; height:318px; margin-left: 12px; margin-bottom:12px'>";
			} else {
				Response += "<div style='width:480px; height:342px'><div style='width:468px; height:318px; margin-left: 12px; margin-bottom:12px; margin-top:12px'>";
			}

			build_Response(Tables[i]);

			Response += "</div></div></td></tr>";
		}

	Response += "</table>";
	
	cout<<Response;

	return 0;
}