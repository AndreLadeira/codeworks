//
//  main.cpp
//  findTheHighlander
//
//  Created by Andre Lima on 5/20/16.
//  Copyright © 2016 AndreLadeira. All rights reserved.
//

#include <iostream>

#include <vector>
//#include <memory>
#include <cmath>

struct node
{
	node * next;
	int val;
	node():val(-1){};
};

int getHighlander(unsigned int N)
{
	if (N <= 1) return 0;
	if (N == 2) return 1;
	
	// build a linked list
	
	node * first = new node;
	node * current = first;
	node * next = 0;
	
	for(unsigned int i = 0; i < N-1; i++)
	{
		next = new node;
		current->val = i;
		current->next = next;
		current = next;
	}
	    
	current->val = N-1;
	current->next = first;
	    
	//debug
	/*current = first;
	
	for (int i = 0; i < N+1; i++)
	{
		std::cout<< current->val << std::endl;
		current = current->next;
	}*/
	
	//play the highlander game...
	//Warning: this code can't be executed over holy ground (churches, temples and alike)
	current = first;
	next    = current->next;
	    
	while (current != next->next)
	{
		current->next = next->next;
		current = next->next;
		
		delete next;
		
		next = current->next;
	}
	    
	// there can be only one
	// printf("%d\n\n",current->val + 1);
	    
	return current->val + 1;
}

int main(int argc, const char * argv[]) 
{
	// insert code here...
	//std::cout << "Hello, World!\n";
	
	for (unsigned int i = 0; i < 200; i++ )
	{
		printf("%d\t%d\n",i,getHighlander(i));
	}
	return 0;
}
